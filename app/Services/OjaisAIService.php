<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OjaisAIService
{
    /*
    |--------------------------------------------------------------------------
    | Analyze food image
    | $imageUrlOrBase64 → URL string অথবা raw base64 string
    |--------------------------------------------------------------------------
    */
    public function analyzeFoodImage(
        string $imageUrlOrBase64,
        string $mimeType = 'image/jpeg'
    ): ?array {

        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            Log::error('OpenAI API key is missing.');
            return null;
        }

        // Image format করো
        $formattedImage = $this->formatImage($imageUrlOrBase64, $mimeType);

        // Label visible কিনা detect করো (URL এ "label" থাকলে বা caller pass করলে)
        // Default false — caller থেকে override করা যাবে
        $userPrompt = $this->userPrompt();

        $payload = [
            'model'      => 'gpt-4o',   // Client fallback হিসেবে GPT-4o রাখো
            'max_tokens' => 2000,
            'messages'   => [
                [
                    'role'    => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $userPrompt,
                        ],
                        [
                            'type'      => 'image_url',
                            'image_url' => [
                                'url'    => $formattedImage,
                                'detail' => 'high', // label clearly read করার জন্য
                            ],
                        ],
                    ],
                ],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature'     => 0.1, // consistent output
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->successful()) {
                $jsonText = $response->json('choices.0.message.content');

                // Markdown code block strip করো
                $jsonText = preg_replace('/```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/```\s*/', '',   $jsonText);

                $data = json_decode(trim($jsonText), true);

                if (empty($data) || empty($data['food_identified'])) {
                    Log::warning('OjaisAIService: empty or invalid response from OpenAI');
                    return null;
                }

                return $this->validateAndClamp($data);
            }

            Log::error('OpenAI API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('OjaisAIService exception: ' . $e->getMessage());
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Image format helper
    |--------------------------------------------------------------------------
    */
    private function formatImage(string $imageUrlOrBase64, string $mimeType): string
    {
        // URL হলে as-is
        if (filter_var($imageUrlOrBase64, FILTER_VALIDATE_URL)) {
            return $imageUrlOrBase64;
        }

        // base64 prefix already আছে কিনা check
        if (preg_match('/^data:image\/\w+;base64,/', $imageUrlOrBase64)) {
            return $imageUrlOrBase64;
        }

        // Raw base64 — prefix add করো
        return 'data:' . $mimeType . ';base64,' . $imageUrlOrBase64;
    }

    /*
    |--------------------------------------------------------------------------
    | Score + verdict validate করো
    |--------------------------------------------------------------------------
    */
    private function validateAndClamp(array $data): array
    {
        // Score 0–100 clamp
        $data['ojais_score']     = max(0, min(100, (int) ($data['ojais_score']     ?? 60)));
        $data['metabolic_score'] = max(0, min(100, (int) ($data['metabolic_score'] ?? 60)));

        // Ojais verdict — client document এর exact tier
        $data['ojais_verdict'] = match (true) {
            $data['ojais_score'] >= 70 => 'Good Choice',
            $data['ojais_score'] >= 45 => 'Neutral Choice',
            default                    => 'Poor Choice',
        };

        // Metabolic verdict — client document এর exact tier
        $data['metabolic_verdict'] = match (true) {
            $data['metabolic_score'] >= 65 => 'Metabolically Protective',
            $data['metabolic_score'] >= 35 => 'Metabolically Neutral',
            default                        => 'Metabolically Harmful',
        };

        // ojais_approved — score >= 70 AND no harmful ingredients
        $data['ojais_approved'] = (
            $data['ojais_score'] >= 70 &&
            empty($data['harmful_ingredients_detected'])
        );

        // Fallback defaults — null হলে safe value দাও
        $data['ingredients_detected']       = $data['ingredients_detected']       ?? [];
        $data['harmful_ingredients_detected'] = $data['harmful_ingredients_detected'] ?? [];
        $data['penalty_flags']              = $data['penalty_flags']              ?? [];
        $data['score_breakdown']            = $data['score_breakdown']            ?? [];
        $data['team_says']                  = $data['team_says']                  ?? '';
        $data['one_improvement']            = $data['one_improvement']            ?? '';
        $data['uncertainty_flag']           = $data['uncertainty_flag']           ?? false;
        $data['nutrition_estimated']        = $data['nutrition_estimated']        ?? [
            'calories'   => 0,
            'protein_g'  => 0,
            'carbs_g'    => 0,
            'fat_g'      => 0,
            'fiber_g'    => 0,
            'sugar_g'    => 0,
            'sodium_mg'  => 0,
        ];

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | System Prompt — client document থেকে exact
    |--------------------------------------------------------------------------
    */
    private function systemPrompt(): string
    {
        return <<<'PROMPT'
You are the Ojais Food Analyzer — an expert nutritionist and food scientist trained to
evaluate food using a real-food-first philosophy. Your job is to analyze a food image
or product label and return a structured JSON score.

CORE PHILOSOPHY: Real, whole, unprocessed food is always rewarded. Industrial
ingredients (seed oils, HFCS, artificial dyes, artificial sweeteners, preservatives,
gums) automatically reduce the score. A processed version of a whole food is NOT
the same as the whole food.

You score every food on TWO dimensions simultaneously:
  (1) Ojais Nutrition Score  0–100
  (2) Metabolic Health Score  0–100  (impact on insulin resistance)

Both scores start at 60. Add points for positives. Deduct for harmful ingredients.
Clamp final scores to 0–100.

════════════════════════════════════
PENALTY INGREDIENTS — Always deduct:
════════════════════════════════════
- HFCS                                       → -25 pts  🔴 Critical
- Artificial food dyes (Red 40, Yellow 5...) → -20 pts  🔴 Critical
- Partially hydrogenated oils / trans fats   → -20 pts  🔴 Critical
- Refined seed oils (soybean/canola/corn/
  cottonseed/sunflower)                      → -15 pts  🔴 High
- Artificial sweeteners (aspartame,
  sucralose, acesulfame-K)                   → -15 pts  🔴 High
- Added refined sugar > 10g per serving      → -12 pts  🔴 High
- Artificial preservatives (BHA/BHT/TBHQ)   → -12 pts  🔴 High
- Corn syrup / corn syrup solids             → -10 pts  🟠 Moderate
- Ultra-processed (10+ industrial ingred.)  → -10 pts  🟠 Moderate
- Maltodextrin / dextrose                    → -8 pts   🟠 Moderate
- Sodium ≥ 800mg per serving                 → -8 pts   🟠 Moderate
- Artificial flavors                         → -6 pts   🟡 Low-Moderate
- Industrial gums (xanthan/guar/CMC)         → -5 pts   🟡 Low

════════════════════════════════════
POSITIVE BOOSTS — Real food rewarded:
════════════════════════════════════
- Single-ingredient whole food               → +30 pts
- Only whole-food ingredients (≤5)           → +20 pts
- Grass-fed / pasture-raised / wild-caught   → +10 pts
- Certified organic label present            → +8 pts
- High fiber (≥ 5g/serving)                  → +8 pts
- Quality whole-food protein (≥ 15g/serving) → +8 pts
- Healthy natural fats (avocado/olive/nuts)  → +6 pts
- Fermented/probiotic food (yogurt/kimchi)   → +6 pts

════════════════════════════════════
CRITICAL RULES:
════════════════════════════════════
- Ojais Approved = true ONLY when score ≥ 70 AND no penalty ingredients found
- Language must be warm, clear, accessible to adults aged 50–75
- Avoid jargon. Be honest but never alarmist. Be specific about WHY.
- If nutrition facts label OR ingredient list is visible in the image,
  READ IT COMPLETELY — the ingredient list takes priority over visual
  appearance for scoring. A product with a healthy front label but
  harmful ingredient list MUST be scored on its INGREDIENTS.
- If image is unclear, set uncertainty_flag to true
PROMPT;
    }

    /*
    |--------------------------------------------------------------------------
    | User Prompt — client document থেকে exact format
    | Label addendum included (client requirement)
    |--------------------------------------------------------------------------
    */
    private function userPrompt(): string
    {
        return <<<'PROMPT'
Analyze this food image and return ONLY valid JSON in this exact format.
No markdown. No text outside the JSON.

If you can see a nutrition facts label or ingredient list, read it completely.
The ingredient list takes priority over visual appearance for scoring.
A product with a healthy front label but harmful ingredient list must be
scored on its INGREDIENTS, not its marketing.

{
  "food_identified": "Descriptive name of what you see",
  "portion_description": "Estimated portion size visible in image",
  "ingredients_detected": ["ingredient1", "ingredient2"],
  "harmful_ingredients_detected": ["any penalty ingredients found"],
  "penalty_flags": ["HFCS detected (-25 pts)", "Soybean oil detected (-15 pts)"],

  "ojais_score": 0,
  "ojais_verdict": "Poor Choice",
  "ojais_approved": false,

  "metabolic_score": 0,
  "metabolic_verdict": "Metabolically Harmful",

  "score_breakdown": {
    "base_score": 60,
    "positive_adjustments": [{"reason": "Single whole food", "points": 30}],
    "negative_adjustments": [{"reason": "HFCS detected", "points": -25}]
  },

  "team_says": "2-3 sentence plain English explanation for a 60-year-old reader.",
  "one_improvement": "One specific actionable change to make this food healthier.",

  "nutrition_estimated": {
    "calories": 0,
    "protein_g": 0,
    "carbs_g": 0,
    "fat_g": 0,
    "fiber_g": 0,
    "sugar_g": 0,
    "sodium_mg": 0
  },

  "uncertainty_flag": false
}
PROMPT;
    }
}
