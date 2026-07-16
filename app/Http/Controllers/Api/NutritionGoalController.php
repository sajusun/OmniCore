<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\NutritionGoal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NutritionGoalController extends Controller
{
    public function show()
    {
        $goal = NutritionGoal::where('user_id', auth('api')->id())->first();

        return response()->json([
            'status' => true,
            'message' => 'Nutrition goal retrieved successfully.',
            'data'    => $goal,
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal'               => 'required|string|max:255',
            'diet_style'         => 'nullable|string|max:255',
            'daily_calories'     => 'nullable|integer|min:0',
            'daily_protein'      => 'nullable|integer|min:0',
            'daily_carbs'        => 'nullable|integer|min:0',
            'daily_fat'          => 'nullable|integer|min:0',
            'weekly_sugar_limit' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $goal = NutritionGoal::updateOrCreate(
            [
                'user_id' => auth('api')->id(),
            ],
            [
                'goal'               => $request->goal,
                'diet_style'         => $request->diet_style,
                'daily_calories'     => $request->daily_calories,
                'daily_protein'      => $request->daily_protein,
                'daily_carbs'        => $request->daily_carbs,
                'daily_fat'          => $request->daily_fat,
                'weekly_sugar_limit' => $request->weekly_sugar_limit,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Nutrition goal saved successfully.',
            'data'    => $goal,
        ]);
    }
}