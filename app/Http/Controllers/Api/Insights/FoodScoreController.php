<?php

namespace App\Http\Controllers\Api\Insights;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\FoodScan;
use Carbon\Carbon;

class FoodScoreController extends Controller
{
    public function index(Request $request)
    {
        $totalScore = 0;
        $scoreCount = 0;
        $statusCounts = [
            'ojais_approved' => 0,
            'red' => 0,
            'neutral' => 0,
        ];

        $allFoods = [];

        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $date = $request->input('date', Carbon::today()->toDateString());

        $foodLogs = \App\Models\FoodLog::where('user_id', $user->id)
            ->whereDate('logged_date', $date)
            ->with('scan')
            ->get();

        $foodScans = collect();
        foreach ($foodLogs as $log) {
            if ($log->scan) {
                $foodScans->push($log->scan);
            }
        }

        $approvedCount = 0;
        $totalScored = 0;

        foreach ($foodScans as $scan) {
            $score = $scan->ojais_score ?? 0;
            if ($score > 0) {
                $totalScore += $score;
                $scoreCount++;
            }

            $status = 'neutral';
            $redFlagReason = null;

            if ($scan) {
                if (!empty($scan->harmful_ingredients)) {
                    $status = 'red';
                    $statusCounts['red']++;
                    $redFlagReason = $scan->team_says_text;
                } elseif ($scan->ojais_approved) {
                    $status = 'ojais_approved';
                    $statusCounts['ojais_approved']++;
                    $approvedCount++;
                } else {
                    $statusCounts['neutral']++;
                }
            } else {
                $statusCounts['neutral']++;
            }

            $totalScored++;

            $allFoods[] = [
                'id' => $scan->id,
                'product_name' => $scan->product_name,
                'image' => $scan->image_url ?? $scan->image ?? null,
                'identify_food' => $scan->identified_foods ?? [],
                'score' => $score,
                'status' => $status,
                'red_flag_reason' => $status === 'red' ? $redFlagReason : null,
            ];
        }



        // if any red flag found then send special message
        if ($statusCounts['red'] > 0) {
            $redFoods = collect($allFoods)->where('status', 'red')->pluck('product_name')->implode(', ');
            $insight = $this->generateFoodQualityInsight($user, $date, $statusCounts['red'], $redFoods);
        } else {
            $insight = "You're doing great! Keep it up.";
        }

        $approvedPercentage = $totalScored > 0 ? round(($approvedCount / $totalScored) * 100) : 0;
        
        $averageScore = number_format($foodScans->avg('ojais_score') ?? 0, 2);

        return response()->json([
            'status' => true,
            'message' => 'Food score details retrieved successfully.',
            'data' => [
                'average_score' => $averageScore,
                'counts' => $statusCounts,
                'insight' => $insight,
                'all_foods' => $allFoods,
            ]
        ]);
    }

    private function generateFoodQualityInsight($user, $date, $redCount, $redFoods)
    {
        $cacheKey = "food_quality_insight_{$user->id}_{$date}_" . md5($redFoods);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($redCount, $redFoods) {
            try {
                $prompt = "The user has scanned and logged $redCount foods with red flags today: $redFoods. Give a very short, 1-2 sentence friendly warning and actionable tip about consuming these items, advising them to choose healthier alternatives.";
                
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
                    return $result['choices'][0]['message']['content'] ?? "You have $redCount red flags today ($redFoods). Try to make healthier choices for your next meal.";
                }
            } catch (\Exception $e) {
                // Fallback
            }
            return "You have $redCount red flags today ($redFoods). Try to make healthier choices for your next meal.";
        });
    }
}
