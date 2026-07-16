<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GeminiVisionService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model');
    }

    /**
     * Analyze an image via Gemini Pro Vision.
     *
     * @param string $filePath Absolute path to the stored image.
     * @return array Parsed JSON result from Gemini.
     * @throws Exception on API error.
     */
    public function analyze(string $filePath): array
    {
        // Convert image to base64
        $mime = mime_content_type($filePath);
        $data = base64_encode(file_get_contents($filePath));

        $prompt = <<<EOT
You are a food recognition and nutrition analysis AI.

Analyze the uploaded image and identify the food or foods present.

Instructions:
First carefully examine the image and mentally zoom into different regions to analyze colors, shapes, and textures before identifying the food.

Image conditions:
The image may be blurry, poorly lit, partially visible, taken at an angle, or contain multiple foods. You must still attempt to identify the most likely foods present.

Rules:
- Identify the most likely foods present even if the image is imperfect.
- If multiple foods appear, list each food separately.
- If the exact food is unclear, estimate the closest likely food category.
- Ignore plates, utensils, tables, packaging, and background objects.
- Focus only on edible food items.
- Use visual clues such as color, texture, and common meal combinations.
- Never refuse analysis due to image quality. Provide the most probable answer.

If the image clearly does not contain food, set identified_foods to ["No food detected"] and ojais_score to 0.

Nutrition scoring guidelines:
10 = whole natural foods (vegetables, fruits, fish, nuts)
7–9 = minimally processed healthy foods
4–6 = moderately processed foods
1–3 = ultra processed foods high in sugar, refined carbs, or unhealthy oils

Portion estimation:
Estimate portion size using visual references such as plate coverage, palm sized portions (~3–4 oz protein), or cup sized portions.

Nutrition rating (1–10):
1 = very unhealthy
10 = very nutritious
Convert this 1–10 rating to a 0–100 ojais_score (multiply by 10).

Estimated category options: "Whole food", "Processed food", "Ultra processed food"

Always analyze whatever food is visible in the frame — even if only part of the meal is shown, analyze what you can see. Only set portion_notes to a retake suggestion if the image is so dark, blurry, or obscured that no food can be identified at all.

Return ONLY valid JSON.
EOT;

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                        ['inlineData' => ['mime_type' => $mime, 'data' => $data]],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ],
            'responseSchema' => [
                'type' => 'object',
                'properties' => [
                    'identified_foods' => ['type' => 'array', 'items' => ['type' => 'string']],
                    'portion_notes' => ['type' => 'string'],
                    'nutrition' => [
                        'type' => 'object',
                        'properties' => [
                            'calories'    => ['type' => 'number'],
                            'protein_g'   => ['type' => 'number'],
                            'carbs_g'     => ['type' => 'number'],
                            'fat_g'       => ['type' => 'number'],
                            'fiber_g'     => ['type' => 'number'],
                            'sugar_g'     => ['type' => 'number'],
                            'sodium_mg'   => ['type' => 'number'],
                        ],
                    ],
                    'ojais_score' => ['type' => 'number'],
                    'verdict' => ['type' => 'string'],
                    'insight' => ['type' => 'string'],
                    'improvement_suggestion' => ['type' => 'string'],
                ],
            ],
        ];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::asJson()->post($url, $payload);

        if ($response->failed()) {
            throw new Exception('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        $candidates = $data['candidates'] ?? [];
        if (empty($candidates)) {
            throw new Exception('Gemini returned no candidates');
        }
        $parts = $candidates[0]['content']['parts'] ?? [];
        $rawJson = null;
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $rawJson = $part['text'];
                break;
            }
        }
        if (!$rawJson) {
            throw new Exception('Gemini did not return JSON text');
        }
        $result = json_decode($rawJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode Gemini JSON: ' . json_last_error_msg());
        }

        // Verdict logic (mirrors Deno snippet)
        $score = $result['ojais_score'] ?? 0;
        if ($score >= 70) {
            $verdictLabel = 'Great Choice';
            $verdictKey = 'ojais_approved';
        } elseif ($score >= 50) {
            $verdictLabel = 'Okay Choice';
            $verdictKey = 'neutral';
        } elseif ($score >= 25) {
            $verdictLabel = 'Poor Choice';
            $verdictKey = 'red';
        } else {
            $verdictLabel = 'Avoid This Meal';
            $verdictKey = 'red';
        }
        $result['verdict_label'] = $verdictLabel;
        $result['verdict_key'] = $verdictKey;

        return $result;
    }
}
?>
