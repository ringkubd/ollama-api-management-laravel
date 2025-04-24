<?php

namespace App\Services;

use App\Models\ApiRequest;
use App\Models\OllamaModel;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OllamaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
    }

    /**
     * List all available models from Ollama API
     *
     * @return array
     */
    public function listModels(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/tags");

            if ($response->successful()) {
                return $response->json('models', []);
            }

            Log::error('Failed to fetch Ollama models', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('Exception when fetching Ollama models', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }

    /**
     * Generate completion using Ollama API
     *
     * @param OllamaModel $model
     * @param array $payload
     * @param string|null $apiKey
     * @param string $ipAddress
     * @param string|null $userAgent
     * @return array
     */
    public function generateCompletion(
        OllamaModel $model,
        array $payload,
        ?string $apiKey = null,
        string $ipAddress = '',
        ?string $userAgent = null
    ): array {
        // Create API request record
        $apiRequest = ApiRequest::create([
            'ollama_model_id' => $model->id,
            'api_key' => $apiKey,
            'endpoint' => '/api/generate',
            'request_payload' => $payload,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => 'processing',
        ]);

        $startTime = microtime(true);

        try {
            // Make sure model name is set in the payload
            $payload['model'] = $model->model_id;

            // Apply model parameters if they exist
            if (!empty($model->parameters)) {
                $payload = array_merge($payload, $model->parameters);
            }

            $response = Http::post("{$this->baseUrl}/api/generate", $payload);
            $responseTime = microtime(true) - $startTime;

            // Update request with response data
            $apiRequest->update([
                'response_payload' => $response->json(),
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'status' => $response->successful() ? 'completed' : 'failed',
                'error_message' => $response->successful() ? null : $response->body(),
            ]);

            // Increment request count for the model
            $model->incrementRequestCount();

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (Exception $e) {
            $responseTime = microtime(true) - $startTime;

            // Update request with error data
            $apiRequest->update([
                'status_code' => 500,
                'response_time' => $responseTime,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Exception in Ollama API request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $apiRequest->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Generate chat completion using Ollama API
     *
     * @param OllamaModel $model
     * @param array $payload
     * @param string|null $apiKey
     * @param string $ipAddress
     * @param string|null $userAgent
     * @return array
     */
    public function generateChatCompletion(
        OllamaModel $model,
        array $payload,
        ?string $apiKey = null,
        string $ipAddress = '',
        ?string $userAgent = null
    ): array {
        // Create API request record
        $apiRequest = ApiRequest::create([
            'ollama_model_id' => $model->id,
            'api_key' => $apiKey,
            'endpoint' => '/api/chat',
            'request_payload' => $payload,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'status' => 'processing',
        ]);

        $startTime = microtime(true);

        try {
            // Make sure model name is set in the payload
            $payload['model'] = $model->model_id;

            // Apply model parameters if they exist
            if (!empty($model->parameters)) {
                $payload = array_merge($payload, $model->parameters);
            }

            $response = Http::post("{$this->baseUrl}/api/chat", $payload);
            $responseTime = microtime(true) - $startTime;

            // Update request with response data
            $apiRequest->update([
                'response_payload' => $response->json(),
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'status' => $response->successful() ? 'completed' : 'failed',
                'error_message' => $response->successful() ? null : $response->body(),
            ]);

            // Increment request count for the model
            $model->incrementRequestCount();

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (Exception $e) {
            $responseTime = microtime(true) - $startTime;

            // Update request with error data
            $apiRequest->update([
                'status_code' => 500,
                'response_time' => $responseTime,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Exception in Ollama chat API request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $apiRequest->id,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Sync models from Ollama API to database
     *
     * @return int Number of models synced
     */
    public function syncModels(): int
    {
        $models = $this->listModels();
        $count = 0;

        foreach ($models as $modelData) {
            if (!isset($modelData['name'])) {
                continue;
            }

            $model = OllamaModel::updateOrCreate(
                ['model_id' => $modelData['name']],
                [
                    'name' => $modelData['name'],
                    'description' => $modelData['name'] . ' model',
                ]
            );

            $count++;
        }

        return $count;
    }
}
