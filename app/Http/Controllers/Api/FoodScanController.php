<?php

namespace App\Http\Controllers\Api;

use App\Models\FoodScan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FoodScannerService;

class FoodScanController extends Controller
{
    public function store(
        Request $request,
        FoodScannerService $foodScanner
    ) {
        $request->validate([
            'image' => ['required', 'image']
        ]);

        $path = $request->file('image')->store('food-scans', 'public');

        $fullPath = storage_path('app/public/' . $path);

        $result = $foodScanner->analyze($fullPath);

        // dd($result);

        $score = $result['ojais_score'] ?? 0;

        $verdict = $this->generateVerdict(
            $score
        );

        $scan = FoodScan::create([
            'user_id' => auth()->id(),

            'image' => $path,

            'identified_foods' =>
            $result['identified_foods'] ?? [],

            'nutrition' =>
            $result['nutrition'] ?? [],

            'ojais_score' =>
            $score,

            'verdict' =>
            $verdict['label'],

            'verdict_key' =>
            $verdict['key'],

            'insight' =>
            $result['insight'] ?? null,

            'improvement_suggestion' =>
            $result['improvement_suggestion'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $scan
        ]);
    }

    private function generateVerdict(
        int $score
    ): array {
        if ($score >= 70) {
            return [
                'label' => 'Great Choice',
                'key' => 'ojais_approved'
            ];
        }

        if ($score >= 50) {
            return [
                'label' => 'Okay Choice',
                'key' => 'neutral'
            ];
        }

        if ($score >= 25) {
            return [
                'label' => 'Poor Choice',
                'key' => 'red'
            ];
        }

        return [
            'label' => 'Avoid This Meal',
            'key' => 'red'
        ];
    }
}
