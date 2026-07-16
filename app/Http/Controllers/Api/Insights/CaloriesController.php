<?php

namespace App\Http\Controllers\Api\Insights;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\FoodLog;
use App\Models\NutritionGoal;
use Carbon\Carbon;

class CaloriesController extends Controller
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

        $totalConsumed = $foodLogs->sum('calories');
        $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        
        // Meals Breakdown
        $mealsBreakdown = collect($mealTypes)->map(function ($type) use ($foodLogs, $totalConsumed) {
            $logs = $foodLogs->filter(function ($log) use ($type) {
                return strtolower($log->meal_type) === $type;
            });
            $calories = $logs->sum('calories');
            return [
                'meal_type' => $type,
                'calories' => $calories,
                'percentage' => $totalConsumed > 0 ? round(($calories / $totalConsumed) * 100) : 0,
            ];
        })->values()->toArray();


        // Weekly Calorie Graph
        $startDate = Carbon::parse($date)->subDays(6)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();
        $weeklyLogs = FoodLog::where('user_id', $user->id)
            ->whereBetween('logged_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('DATE(logged_date) as date_string, SUM(calories) as total_cals')
            ->groupBy('date_string')
            ->orderBy('date_string', 'ASC')
            ->get()
            ->keyBy('date_string');

        $dailyGraph = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = clone $startDate;
            $currentDate->addDays($i);
            $formattedDate = $currentDate->toDateString();
            $dailyGraph[] = [
                'day' => $currentDate->format('D'),
                'calories' => isset($weeklyLogs[$formattedDate]) ? (float) $weeklyLogs[$formattedDate]->total_cals : 0,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Calories details retrieved successfully.',
            'data' => [
                'total_consumed' => $totalConsumed,
                'meals_breakdown' => $mealsBreakdown,
                'daily_graph' => $dailyGraph,
                'calorie_insight' => $this->generateCalorieInsight($user, $date, $totalConsumed, $goals->daily_calories, $mealsBreakdown),
            ]
        ]);
    }

    private function generateCalorieInsight($user, $date, $consumedCals, $targetCals, $mealsBreakdown)
    {
        $cacheKey = "calorie_insight_user_{$user->id}_date_{$date}_cals_{$consumedCals}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($consumedCals, $targetCals, $mealsBreakdown) {
            try {
                $breakdownText = "";
                foreach ($mealsBreakdown as $meal) {
                    if ($meal['calories'] > 0) {
                        $breakdownText .= ucfirst($meal['meal_type']) . ": {$meal['percentage']}%, ";
                    }
                }
                
                $prompt = "You are a smart nutrition assistant. The user consumed {$consumedCals} calories out of a target of {$targetCals} today. Their meal breakdown is: {$breakdownText}. Write a very short, 1-2 sentence motivating insight. Point out if they are eating too much in one meal or doing great balancing their calories across the day. Keep it natural and encouraging.";
                
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
                    return $result['choices'][0]['message']['content'] ?? "Great job tracking your calories! Keep it up to reach your goals.";
                }
            } catch (\Exception $e) {
                // Fallback on error
            }

            return "Great job tracking your calories! Keep it up to reach your goals.";
        });
    }
}
