<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Base44VisionService
{
    protected $apiKey;
    protected $endpoint = 'https://api.base44.com/v1/vision/food';

    public function __construct()
    {
        $this->apiKey = config('services.base44.key');
    }

    /**
     * Send image file to Base44 Vision API and return parsed JSON.
     * @param string $filePath Absolute path to the stored image.
     * @return array
     */
    public function analyze(string $filePath): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->attach(
            'image',
            file_get_contents($filePath),
            basename($filePath)
        )->post($this->endpoint);

        if ($response->failed()) {
            throw new \Exception('Base44 Vision API error: ' . $response->body());
        }

        return $response->json();
    }
}
?>
