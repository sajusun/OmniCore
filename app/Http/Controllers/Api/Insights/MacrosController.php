<?php

namespace App\Http\Controllers\Api\Insights;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\FoodLog;
use App\Models\NutritionGoal;
use Carbon\Carbon;

class MacrosController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $goals = NutritionGoal::where('user_id', $user->id)->first();
        if (!$goals) {
            return response()->json(['status' => false, 'message' => 'Please set your nutrition goals first.'], 400);
        }

        $foodLogs = FoodLog::where('user_id', $user->id)
            ->whereDate('logged_date', $date)
            ->get();

        $consumed = [
            'carbs' => $foodLogs->sum('carbs_g'),
            'protein' => $foodLogs->sum('protein_g'),
            'fat' => $foodLogs->sum('fat_g'),
        ];
        
        $totalMacrosGrams = $consumed['carbs'] + $consumed['protein'] + $consumed['fat'];

        // Percentages of total macros (based on grams)
        $carbsPercent = $totalMacrosGrams > 0 ? round(($consumed['carbs'] / $totalMacrosGrams) * 100) : 0;
        $proteinPercent = $totalMacrosGrams > 0 ? round(($consumed['protein'] / $totalMacrosGrams) * 100) : 0;
        $fatPercent = $totalMacrosGrams > 0 ? round(($consumed['fat'] / $totalMacrosGrams) * 100) : 0;

        // Details for the progress bars
        $details = [
            [
                'name' => 'PROTEIN',
                'consumed' => $consumed['protein'],
                'target' => $goals->daily_protein,
                'percent_of_total_macros' => $proteinPercent,
                'percent_of_target' => $goals->daily_protein > 0 ? round(($consumed['protein'] / $goals->daily_protein) * 100) : 0,
            ],
            [
                'name' => 'CARBS',
                'consumed' => $consumed['carbs'],
                'target' => $goals->daily_carbs,
                'percent_of_total_macros' => $carbsPercent,
                'percent_of_target' => $goals->daily_carbs > 0 ? round(($consumed['carbs'] / $goals->daily_carbs) * 100) : 0,
            ],
            [
                'name' => 'FAT',
                'consumed' => $consumed['fat'],
                'target' => $goals->daily_fat,
                'percent_of_total_macros' => $fatPercent,
                'percent_of_target' => $goals->daily_fat > 0 ? round(($consumed['fat'] / $goals->daily_fat) * 100) : 0,
            ]
        ];

        $insight = $this->generateMacroInsight($user, $date, $consumed, $goals);

        // Summary
        $summary = [
            'total_macros_g' => $totalMacrosGrams,
            'protein_calories' => $consumed['protein'] * 4,
            'carb_calories' => $consumed['carbs'] * 4,
            'fat_calories' => $consumed['fat'] * 9,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Macro details retrieved successfully.',
            'data' => [
                'distribution' => [
                    'carbs_percent' => $carbsPercent,
                    'protein_percent' => $proteinPercent,
                    'fat_percent' => $fatPercent,
                ],
                'details' => $details,
                'summary' => $summary,
                'insight' => $insight,
            ]
        ]);
    }

    private function generateMacroInsight($user, $date, $consumed, $goals)
    {
        $protein = $consumed['protein'];
        $carbs = $consumed['carbs'];
        $fat = $consumed['fat'];
        
        $targetProtein = $goals->daily_protein;
        $targetCarbs = $goals->daily_carbs;
        $targetFat = $goals->daily_fat;
        
        $cacheKey = "macro_insight_{$user->id}_{$date}_{$protein}_{$carbs}_{$fat}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($protein, $carbs, $fat, $targetProtein, $targetCarbs, $targetFat) {
            try {
                $prompt = "User macros today: Protein {$protein}g (target {$targetProtein}g), Carbs {$carbs}g (target {$targetCarbs}g), Fat {$fat}g (target {$targetFat}g). Provide a very short, 1-2 sentence insight about their macro balance. Be encouraging and provide a specific tip on what to eat more or less of today.";
                
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a helpful nutrition assistant.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 80,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    return $result['choices'][0]['message']['content'] ?? "You're on the right track! Focus on hitting your protein and balancing carbs and fats.";
                }
            } catch (\Exception $e) {
                // Fallback
            }
            return "You're on the right track! Focus on hitting your protein and balancing carbs and fats.";
        });
    }
}
