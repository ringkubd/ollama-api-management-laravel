<?php

namespace App\Services;

use App\Models\ApiRequest;
use App\Models\OllamaModel;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OllamaCompletionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
    }

    /**
     * Generate chat completion with Ollama API
     *
     * @param OllamaModel $model
     * @param array $messages
     * @param float|null $temperature
     * @param int|null $maxTokens
     * @return array
     */
    public function generateChatCompletion(
        OllamaModel $model,
        array $messages,
        ?float $temperature = 0.7,
        ?int $maxTokens = 1024
    ): array {
        // Build the chat completion payload
        $payload = [
            'model' => $model->model_id,
            'messages' => $messages,
            'stream' => false, // We don't want streaming for the playground
            'options' => [
                'temperature' => $temperature,
                'num_predict' => $maxTokens,
            ]
        ];

        // Create API request record
        $apiRequest = ApiRequest::create([
            'ollama_model_id' => $model->id,
            'api_key' => null,
            'endpoint' => '/api/chat',
            'request_payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'processing',
        ]);

        $startTime = microtime(true);

        try {
            // Apply model parameters if they exist
            if (!empty($model->parameters)) {
                $payload['options'] = array_merge($payload['options'], $model->parameters);
            }

            $response = Http::timeout(120)->post("{$this->baseUrl}/api/chat", $payload);
            $responseTime = microtime(true) - $startTime;

            // Check if the response was successful
            if (!$response->successful()) {
                throw new Exception("Ollama API error: " . $response->body());
            }

            $responseData = $response->json();

            // Additional validation to ensure we have actual content
            if (empty($responseData['message']['content']) && isset($responseData['done_reason']) && $responseData['done_reason'] === 'load') {
                // The model was only loaded but didn't generate content
                Log::warning('Ollama only loaded the model but did not generate content', [
                    'model' => $model->model_id,
                    'response' => $responseData
                ]);

                // Try one more time now that model is loaded
                $response = Http::timeout(120)->post("{$this->baseUrl}/api/chat", $payload);
                $responseTime = microtime(true) - $startTime;

                if (!$response->successful()) {
                    throw new Exception("Ollama API error on retry: " . $response->body());
                }

                $responseData = $response->json();
            }

            // Update request with response data
            $apiRequest->update([
                'response_payload' => $responseData,
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'status' => 'completed',
                'error_message' => null,
            ]);

            // Increment request count for the model
            $model->incrementRequestCount();

            return $responseData;
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

            throw $e;
        }
    }

    /**
     * Generate text completion with Ollama API
     *
     * @param OllamaModel $model
     * @param string $prompt
     * @param int|null $maxTokens
     * @param float|null $temperature
     * @return array
     */
    public function generateCompletion(
        OllamaModel $model,
        string $prompt,
        ?int $maxTokens = 1024,
        ?float $temperature = 0.7
    ): array {
        // Build the completion payload
        $payload = [
            'model' => $model->model_id,
            'prompt' => $prompt,
            'stream' => false, // We don't want streaming for the playground
            'options' => [
                'temperature' => $temperature,
                'num_predict' => $maxTokens,
            ]
        ];

        // Create API request record
        $apiRequest = ApiRequest::create([
            'ollama_model_id' => $model->id,
            'api_key' => null,
            'endpoint' => '/api/generate',
            'request_payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'processing',
        ]);

        $startTime = microtime(true);

        try {
            // Apply model parameters if they exist
            if (!empty($model->parameters)) {
                $payload['options'] = array_merge($payload['options'], $model->parameters);
            }

            $response = Http::timeout(120)->post("{$this->baseUrl}/api/generate", $payload);
            $responseTime = microtime(true) - $startTime;

            // Check if the response was successful
            if (!$response->successful()) {
                throw new Exception("Ollama API error: " . $response->body());
            }

            $responseData = $response->json();

            // Additional validation to ensure we have actual content
            if (empty($responseData['response']) && isset($responseData['done_reason']) && $responseData['done_reason'] === 'load') {
                // The model was only loaded but didn't generate content
                Log::warning('Ollama only loaded the model but did not generate content', [
                    'model' => $model->model_id,
                    'response' => $responseData
                ]);

                // Try one more time now that model is loaded
                $response = Http::timeout(120)->post("{$this->baseUrl}/api/generate", $payload);
                $responseTime = microtime(true) - $startTime;

                if (!$response->successful()) {
                    throw new Exception("Ollama API error on retry: " . $response->body());
                }

                $responseData = $response->json();
            }

            // Update request with response data
            $apiRequest->update([
                'response_payload' => $responseData,
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'status' => 'completed',
                'error_message' => null,
            ]);

            // Increment request count for the model
            $model->incrementRequestCount();

            return $responseData;
        } catch (Exception $e) {
            $responseTime = microtime(true) - $startTime;

            // Update request with error data
            $apiRequest->update([
                'status_code' => 500,
                'response_time' => $responseTime,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Exception in Ollama completion API request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $apiRequest->id,
            ]);

            throw $e;
        }
    }
}
