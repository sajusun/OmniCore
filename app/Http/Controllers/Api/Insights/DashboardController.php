<?php

namespace App\Http\Controllers\Api\Insights;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\FoodLog;
use App\Models\FoodScan;
use App\Models\NutritionGoal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    

    public function index(Request $request)
    {

        $user = auth('api')->user();
        $date = $request->input('date', Carbon::today()->toDateString());
        


        /* start insight calories information  */
        $goals = NutritionGoal::where('user_id', $user->id)->first();
        
        if (!$goals) {
            return response()->json(['status' => false, 'message' => 'Please set your nutrition goals first.'], 400);
        }

    
        $foodLogs = FoodLog::where('user_id', $user->id)
            ->whereDate('logged_date', $date)
            ->with('scan')
            ->get();

        $consumedCals = $foodLogs->sum('calories');
        $burnedCals = 0;
        $leftCals = max(0, $goals->daily_calories - $consumedCals + $burnedCals);


        /* end insight calories information  */



        /* start insight macros information */
        $macrosLogs = FoodLog::where('user_id', $user->id)
            ->whereDate('logged_date', $date)
            ->with('scan')
            ->get();

        $macros = [
            'carbs' => [
                'consumed' => $macrosLogs->sum('carbs_g'),
                'target' => $goals->daily_carbs,
                'left' => max(0, $goals->daily_carbs - $macrosLogs->sum('carbs_g')),
            ],
            'fat' => [
                'consumed' => $macrosLogs->sum('fat_g'),
                'target' => $goals->daily_fat,
                'left' => max(0, $goals->daily_fat - $macrosLogs->sum('fat_g')),
            ],
            'protein' => [
                'consumed' => $macrosLogs->sum('protein_g'),
                'target' => $goals->daily_protein,
                'left' => max(0, $goals->daily_protein - $macrosLogs->sum('protein_g')),
            ]
            ];
        /* end insight macros information */

        /* today insight */

        // Calculate food quality based on what the user actually ATE (FoodLogs)
        $totalEatenScans = 0;
        $approvedEaten = 0;

        foreach ($foodLogs as $log) {
            if ($log->scan) {
                $totalEatenScans++;
                if ($log->scan->ojais_approved) {
                    $approvedEaten++;
                }
            }
        }

        $approvedPercentage = $totalEatenScans > 0 ? round(($approvedEaten / $totalEatenScans) * 100, 2) : 0;

        if ($date === Carbon::today()->toDateString()) {
            $todayInsight = $this->generateTodaysInsight(
                $user, 
                $date, 
                $consumedCals, 
                $goals->daily_calories, 
                $approvedPercentage
            );
        } else {
            $todayInsight = null;
        }

        /* food quality score */
        $totalEatenScore = 0;
        $scoredItemsCount = 0;
        $redFlagItems = [];

        foreach ($foodLogs as $log) {
            if ($log->scan) {
                $score = $log->scan->ojais_score ?? 0;
                if ($score > 0) {
                    $totalEatenScore += $score;
                    $scoredItemsCount++;
                }

                if (!empty($log->scan->harmful_ingredients)) {
                    $redFlagItems[] = $log->scan->product_name ?? 'Unknown item';
                }
            }
        }

        $averageScore = $scoredItemsCount > 0 ? (float) number_format($totalEatenScore / $scoredItemsCount, 2) : 0;

        // $foodQualityWarning = null;
        // if (!empty($redFlagItems) && $date === Carbon::today()->toDateString()) {
        //     $foodQualityWarning = $this->generateRedFlagWarning($user, $date, $redFlagItems);
        // }



        /* weekly trend  */

         // Weekly Trend
        $startDate = Carbon::parse($date)->subDays(6)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();
        $weeklyLogs = FoodLog::where('user_id', $user->id)
            ->whereBetween('logged_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('DATE(logged_date) as date_string, SUM(calories) as total_cals')
            ->groupBy('date_string')
            ->orderBy('date_string', 'ASC')
            ->get()
            ->keyBy('date_string');

        $weeklyTrends = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = clone $startDate;
            $currentDate->addDays($i);
            $formattedDate = $currentDate->toDateString();
            $weeklyTrends[] = [
                'day' => $currentDate->format('D'),
                'calories' => isset($weeklyLogs[$formattedDate]) ? (float) $weeklyLogs[$formattedDate]->total_cals : 0,
            ];
        }

        








        // Recommendations / Smart Tips
        $recommendations = $this->generateSmartRecommendations($user, $date, [
            'consumed_calories' => $consumedCals,
            'target_calories' => $goals->daily_calories,
            'approved_percentage' => $approvedPercentage,
            'macros' => [
                'protein' => ['consumed' => $macrosLogs->sum('protein_g'), 'target' => $goals->daily_protein],
                'carbs' => ['consumed' => $macrosLogs->sum('carbs_g'), 'target' => $goals->daily_carbs],
                'fat' => ['consumed' => $macrosLogs->sum('fat_g'), 'target' => $goals->daily_fat],
            ]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Dashboard insights retrieved successfully.',
            'data' => [
                'calories' => [
                    'target' => $goals->daily_calories,
                    'consumed' => $consumedCals,
                    'burned' => $burnedCals,
                    'left' => $leftCals,
                ],
                'macros' => $macros,
                'today_insight' => $todayInsight,
                'food_quality' => [
                    'average_score' => $averageScore,
                    'approved_percentage' => $approvedPercentage,
                    'not_approved_percentage' => $totalEatenScans > 0 ? 100 - $approvedPercentage : 0,
                    // 'red_flag_items' => array_unique($redFlagItems),
                    // 'red_flag_warning' => $foodQualityWarning,
                ],

                'weekly_trends' => $weeklyTrends,
                'smart_tips' => $recommendations['tips'] ?? [],
            ]
        ]);
    }




    private function generateTodaysInsight($user, $date, $consumedCals, $targetCals, $approvedPercentage)
    {
        $cacheKey = "todays_insight_user_{$user->id}_date_{$date}_cals_{$consumedCals}_score_{$approvedPercentage}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($consumedCals, $targetCals, $approvedPercentage) {
            try {
                $prompt = "You are a friendly and encouraging nutrition assistant. The user consumed {$consumedCals} calories today out of a target of {$targetCals}. Their healthy food quality score (based on the foods they actually ate) is {$approvedPercentage}%. Write a very short, 1-2 sentence motivating insight. If their score is high, praise their healthy food choices. If their calories are fine but their score is low, gently warn them that their food choices contained unhealthy items and they should be careful. Make it natural and highly contextual to their exact numbers.";
                
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a helpful and concise nutrition assistant.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 60,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    return $result['choices'][0]['message']['content'] ?? "Great job! Keep tracking your meals to stay on target.";
                }
            } catch (\Exception $e) {
                // Fallback on error
            }

            return "Great job! Keep tracking your meals to stay on target.";
        });
    }

    private function generateRedFlagWarning($user, $date, $redFlagItems)
    {
        $itemsList = implode(', ', array_unique($redFlagItems));
        $cacheKey = "red_flag_warning_user_{$user->id}_date_{$date}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($itemsList) {
            try {
                $prompt = "The user consumed the following foods today which contained harmful or 'red flag' ingredients: {$itemsList}. Write a very short, 1-2 sentence warning and tip explaining why they should avoid these and how to choose healthier alternatives tomorrow. Keep it friendly but firm.";
                
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
                    return $result['choices'][0]['message']['content'] ?? null;
                }
            } catch (\Exception $e) {
                // Fallback on error
            }

            return "Please be careful with foods containing red flags tomorrow. Try choosing more natural, whole foods.";
        });
    }












        private function generateSmartRecommendations($user, $date, $data)
    {
        $cals = $data['consumed_calories'];
        $target = $data['target_calories'];
        $score = $data['approved_percentage'] ?? 0;
        
        $macros = $data['macros'] ?? null;
        $macrosPrompt = '';
        if ($macros) {
            $macrosPrompt = "Macros: Protein {$macros['protein']['consumed']}g/{$macros['protein']['target']}g, Carbs {$macros['carbs']['consumed']}g/{$macros['carbs']['target']}g, Fat {$macros['fat']['consumed']}g/{$macros['fat']['target']}g. ";
        }

        $cacheKey = "smart_recommendations_user_{$user->id}_date_{$date}_cals_{$cals}_score_{$score}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($cals, $target, $score, $macrosPrompt) {
            $defaultTips = [
                "Stay consistent with your logging!",
                "Try to balance your meals better today."
            ];

            try {
                $prompt = "You are a smart nutrition assistant. The user has consumed {$cals} calories out of a target of {$target}. Their food quality score is {$score}%. {$macrosPrompt} Provide exactly 4 short, actionable nutrition recommendations or tips based on their entire day's food log in JSON format like this: {\"tips\": [\"Tip 1\", \"Tip 2\", \"Tip 3\", \"Tip 4\"]}. Keep the tips under 15 words each. Focus on how to improve or maintain their current stats.";
                
                $response = Http::withToken(env('OPENAI_API_KEY'))
                    ->timeout(10)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-3.5-turbo',
                        'response_format' => ['type' => 'json_object'],
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a helpful nutrition assistant that outputs JSON.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'max_tokens' => 150,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $content = $result['choices'][0]['message']['content'] ?? '';
                    $json = json_decode($content, true);
                    if (isset($json['tips']) && is_array($json['tips'])) {
                        return [
                            'ai_insights_ready' => true,
                            'tips' => $json['tips']
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Fallback on error
            }

            return [
                'ai_insights_ready' => false,
                'tips' => $defaultTips
            ];
        });
    }

}
