<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FoodScannerService
{
    public function analyze(string $imagePath): array
    {
        $imageData = base64_encode(
            file_get_contents($imagePath)
        );

        $prompt = file_get_contents(
            storage_path('prompts/food_scan.txt')
        );

        $response = Http::timeout(180)
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key="
                    . config('services.gemini.key'),
                [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => $prompt
                                ],
                                [
                                    "inline_data" => [
                                        "mime_type" => "image/jpeg",
                                        "data" => $imageData
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $response = $response->json();
        dd($response);

        $text = data_get(
            $response,
            'candidates.0.content.parts.0.text'
        );

        return json_decode($text, true);
    }
}
