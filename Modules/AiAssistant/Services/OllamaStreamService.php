<?php

namespace Modules\AiAssistant\Services;

use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaStreamService
{
    protected Client $client;

    public function __construct(
        protected string $baseUrl,
        protected string $defaultModel,
        protected int $timeout = 120
    ) {
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => $this->timeout,
        ]);
    }

    /**
     * List locally installed Ollama models
     */
    public function listLocalModels(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");
            if ($response->successful()) {
                $models = collect($response->json('models', []))
                    ->pluck('name')
                    ->toArray();

                if (!empty($models)) {
                    return $models;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Failed to fetch Ollama models: " . $e->getMessage());
        }

        return [$this->defaultModel];
    }

    /**
     * Stream responses from Ollama's /api/chat endpoint
     *
     * @param string $model
     * @param array $messages Array of ['role' => '...', 'content' => '...']
     * @param array $options Model runtime options (temperature, num_ctx, etc.)
     * @return Generator Yields associative arrays with chunk information
     */
    public function streamChat(string $model, array $messages, array $options = []): Generator
    {
        $payload = [
            'model' => $model ?: $this->defaultModel,
            'messages' => $messages,
            'stream' => true,
            'options' => array_merge([
                'temperature' => 0.7,
            ], $options),
        ];

        try {
            $response = $this->client->post('/api/chat', [
                'json' => $payload,
                'stream' => true,
            ]);

            $body = $response->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(512);
                $buffer .= $chunk;

                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if (empty($line)) {
                        continue;
                    }

                    $json = json_decode($line, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($json['message']['content'])) {
                        yield [
                            'content' => $json['message']['content'],
                            'done'    => $json['done'] ?? false,
                            'metrics' => ($json['done'] ?? false) ? [
                                'total_duration' => isset($json['total_duration']) ? round($json['total_duration'] / 1_000_000) : null,
                                'eval_count'     => $json['eval_count'] ?? null,
                            ] : null,
                        ];
                    }
                }
            }
        } catch (GuzzleException $e) {
            Log::error("Ollama connection error: " . $e->getMessage());
            yield [
                'error' => 'Connection to Ollama failed: ' . $e->getMessage(),
                'done'  => true,
            ];
        }
    }
}
