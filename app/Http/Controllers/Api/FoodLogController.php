<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\FoodLog;
use App\Models\Product;
use App\Models\FoodScan;
use Illuminate\Http\Request;
use App\Models\NutritionGoal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FoodLogController extends Controller
{
    /**
     * Store a newly created food log in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_type' => 'required|in:scan,search,manual',
            // only one model get
            'product_id'   => 'nullable|exists:products,id',    // nullable 
            'food_scan_id'  => 'nullable|exists:food_scans,id', // nullable 

            'product_name'  => 'required|string|max:255',
            'meal_type'     => 'required|in:breakfast,lunch,dinner,snack',
            'serving_size'  => 'required|numeric|min:0.1',
            'base_calories' => 'required|numeric|min:0',
            'base_protein_g' => 'required|numeric|min:0',
            'base_carbs_g'  => 'required|numeric|min:0',
            'base_fat_g'    => 'required|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {

            if ($request->source_type === 'scan' && !$request->food_scan_id) {
                $validator->errors()->add('food_scan_id', 'Food scan is required.');
            }

            if ($request->source_type === 'product' && !$request->product_id) {
                $validator->errors()->add('product_id', 'Product is required.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['status'  => false, 'message' => 'Validation error', 'errors'  => $validator->errors()], 422);
        }

        $image = null;
        $foodScore = null;

        if ($request->source_type === 'scan') {
            $foodScan = FoodScan::find($request->food_scan_id);
            if (!$foodScan) {
                return response()->json(['status'  => false, 'message' => 'Food scan not found',], 404);
            }
            $image = $foodScan->image_url;
            $foodScore = $foodScan->ojais_score;
        } elseif ($request->source_type === 'search') {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['status'  => false, 'message' => 'Search Food not found',], 404);
            }
            $image = $product->image_url ?? null;
            $foodScore = $product->score ?? null;
        }


        $serving_size = $request->serving_size;
        $now = Carbon::now();

        $foodLog = FoodLog::create([
            'user_id'      => auth('api')->id(),

            'source_type'   => $request->source_type,
            'food_scan_id'  => $request->food_scan_id,
            'product_id'    => $request->product_id,

            'product_name' => $request->product_name,
            'image'        => $image,
            'meal_type'    => $request->meal_type,
            'serving_size' => $serving_size,
            'calories'     => round($request->base_calories * $serving_size),
            'protein_g'    => round($request->base_protein_g * $serving_size),
            'carbs_g'      => round($request->base_carbs_g * $serving_size),
            'fat_g'        => round($request->base_fat_g * $serving_size),
            'food_score'   => $foodScore,
            'logged_date'  => $now->format('Y-m-d'),
            'logged_time'  => $now->format('H:i:s'),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Food log store successfully',
            'data'    => $foodLog,
            'code' => 200
        ], 201);
    }


    // update food log
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_log_id' => 'required|exists:food_logs,id',
            'meal_type'     => 'required|in:breakfast,lunch,dinner,snack',
            'serving_size'  => 'required|numeric|min:0.1',
            'base_calories' => 'required|numeric|min:0',
            'base_protein_g' => 'required|numeric|min:0',
            'base_carbs_g'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $serving_size = $request->serving_size;
        $now = Carbon::now();

        $foodLog = FoodLog::where('id', $request->food_log_id)->first();

        if (!$foodLog) {
            return response()->json([
                'status'  => false,
                'message' => 'Food log not found',
            ], 404);
        }

        $foodLog->meal_type = $request->meal_type;
        $foodLog->serving_size = $serving_size;
        $foodLog->calories = round($request->base_calories * $serving_size);
        $foodLog->protein_g = round($request->base_protein_g * $serving_size);
        $foodLog->carbs_g = round($request->base_carbs_g * $serving_size);
        $foodLog->logged_date = $now->format('Y-m-d');
        $foodLog->logged_time = $now->format('H:i:s');
        $foodLog->save();

        return response()->json([
            'status'  => true,
            'message' => 'Food log updated successfully',
            'data'    => $foodLog,
            'code' => 200
        ], 201);
    }



















    /**
     * Display the specified resource for a specific date.
     */
    public function index(Request $request)
    {
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $userId = auth('api')->id();

        $logs = FoodLog::where('user_id', $userId)
            ->where('logged_date', $date)
            ->orderBy('logged_time', 'asc')
            ->get();



        $consumed = [
            'calories'  => $logs->sum('calories'),
            'protein_g' => $logs->sum('protein_g'),
            'carbs_g'   => $logs->sum('carbs_g'),
            'fat_g'     => $logs->sum('fat_g'),
        ];

        $goal = NutritionGoal::where('user_id', $userId)->first();
        $targets = [
            'calories'  => $goal ? $goal->daily_calories : 2000,
            'protein_g' => $goal ? $goal->daily_protein : 150,
            'carbs_g'   => $goal ? $goal->daily_carbs : 200,
            'fat_g'     => $goal ? $goal->daily_fat : 70,
        ];

        $meals = [
            'breakfast' => $logs->where('meal_type', 'breakfast')->values(),
            'lunch'     => $logs->where('meal_type', 'lunch')->values(),
            'dinner'    => $logs->where('meal_type', 'dinner')->values(),
            'snack'     => $logs->where('meal_type', 'snack')->values(),
        ];


        $recent_scan_foods = FoodScan::where('user_id', $userId)->orderBy('id', 'desc')->limit(5)->get();


        return response()->json([
            'status' => true,
            'message' => 'Daily diary retrieved successfully',
            'data'   => [
                'date'    => $date,
                'summary' => [
                    'consumed' => $consumed,
                    'targets'  => $targets,
                ],
                'meals'   => $meals,
                'recent_scan_foods' => $recent_scan_foods,
            ]
        ], 200);
    }

    /**
     * Remove the specified food log from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_log_id' => 'required|exists:food_logs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $log = FoodLog::where('user_id', auth('api')->id())->find($request->food_log_id);

        if (!$log) {
            return response()->json([
                'status'  => false,
                'message' => 'Food log not found',
                'data'    => [],
                'code'    => 404
            ], 404);
        }

        $log->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Food log deleted successfully',
            'data'    => [],
            'code'    => 200
        ], 200);
    }
}
