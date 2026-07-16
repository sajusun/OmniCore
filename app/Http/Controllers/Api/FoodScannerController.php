<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\FoodScan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\OjaisAIService;
use App\Http\Controllers\Controller;

class FoodScannerController extends Controller
{
    /**
     * Scan food image using Gemini AI.
     */
    public function scanImage(Request $request, OjaisAIService $aiService)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imageUrlOrBase64 = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Convert uploaded file to base64 for Gemini
            $imageUrlOrBase64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->path()));
        } else {
            $imageUrlOrBase64 = $request->input('image_url');
        }

        if (!$imageUrlOrBase64) {
            return response()->json(['error' => 'No image provided'], 400);
        }


        $result = $aiService->analyzeFoodImage($imageUrlOrBase64);

        if (!$result) {
            return response()->json(['error' => 'Failed to analyze image'], 500);
        }


        // file upload handling (optional, if you want to store the image)
        $storedImageUrl = null;
        if ($request->hasFile('image')) {
            $storedImageUrl = Helper::fileUpload($request->file('image'), 'food_scans', 'food_scan_' . time());
        }




        // Store the result
        $foodScan = FoodScan::create([
            'user_id' => $request->user()->id ?? null,
            'image_url' => $storedImageUrl,
            'product_name' => $result['food_identified'] ?? null,
            'identified_foods' => $result['ingredients_detected'] ?? null,
            'ojais_score' => $result['ojais_score'] ?? null,
            'verdict_label' => $result['ojais_verdict'] ?? null,
            'verdict_key' => isset($result['ojais_verdict']) ? strtolower(str_replace(' ', '_', $result['ojais_verdict'])) : null,
            'ojais_approved' => $result['ojais_approved'] ?? false,
            'metabolic_score' => $result['metabolic_score'] ?? null,
            'metabolic_verdict' => $result['metabolic_verdict'] ?? null,
            'harmful_ingredients' => $result['harmful_ingredients_detected'] ?? null,
            'penalty_flags' => $result['penalty_flags'] ?? null,
            'score_breakdown' => $result['score_breakdown'] ?? null,
            'portion_estimation' => $result['portion_description'] ?? null,
            'nutrition' => $result['nutrition_estimated'] ?? null,
            'team_says_text' => $result['team_says'] ?? null,
            'one_improvement' => $result['one_improvement'] ?? null,
            'uncertainty_flag' => $result['uncertainty_flag'] ?? false,
        ]);

        $cards = $this->formatCards($foodScan);

        return response()->json([
            'message' => 'Image analyzed successfully',
            'data' => [
                'scan_id' => $foodScan->id,
                'image_url' => $foodScan->image_url,
                'cards' => $cards,
                // 'raw_scan' => $foodScan,
            ]
        ], 200);
    }


    public function getFoodScan($id)
    {

        $foodScan = FoodScan::find($id);

        if (!$foodScan) {
            return response()->json([
                'status' => false,
                'message' => 'Food scan not found',
                'code' => 404
            ], 404);
        }

        $cards = $this->formatCards($foodScan);

        return response()->json([
            'status' => true,
            'message' => 'Food scan found',
            'data' => [
                'id' => $foodScan->id,
                'image_url' => $foodScan->image_url,
                'cards' => $cards,
                // 'raw_scan' => $foodScan,
            ]
        ], 200);
    }

    private function formatCards($foodScan)
    {
        return [
            'product' => [
                'product_name' => $foodScan->product_name,
                'identified_foods' => $foodScan->identified_foods,
            ],
            'ojais_score_card' => [
                'score' => $foodScan->ojais_score,
                'verdict' => $foodScan->verdict_label,
                'portion_description' => $foodScan->portion_estimation,
                'ojais_approved' => (bool)$foodScan->ojais_approved,
            ],
            'metabolic_health_card' => [
                'score' => $foodScan->metabolic_score,
                'verdict' => $foodScan->metabolic_verdict,
                'score_breakdown' => $foodScan->score_breakdown,
            ],
            'foods_identified' => [
                'ingredients_detected' => $foodScan->identified_foods,
                'harmful_ingredients_detected' => $foodScan->harmful_ingredients,
                'penalty_flags' => $foodScan->penalty_flags,
            ],
            'estimated_nutrition' => [
                'nutrition' => $foodScan->nutrition,
                'score_breakdown' => $foodScan->score_breakdown,
            ],
            'ojais_team_says' => [
                'body' => $foodScan->team_says_text,
                'verdict' => $foodScan->verdict_label,
                'uncertainty_flag' => (bool)$foodScan->uncertainty_flag,
            ],
            'one_improvement' => [
                'tip' => $foodScan->one_improvement,
            ]
        ];
    }

    public function scanHistory(Request $request)
    {
        $query = FoodScan::query()->select(['id','image_url','product_name','identified_foods','verdict_label'])->where('user_id', auth('api')->user()->id)->latest();

        $foodScans = $request->boolean('all') ? $query->get() : $query->take(4)->get();

        return $this->success($foodScans, 'Food scan history fetched successfully');
    }
}
