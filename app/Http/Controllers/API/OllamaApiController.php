<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OllamaModel;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessOllamaRequest;

class OllamaApiController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * Generate a completion using specified model
     *
     * @param Request $request
     * @param string $modelId
     * @return JsonResponse
     */
    public function generate(Request $request, string $modelId): JsonResponse
    {
        $model = OllamaModel::where('model_id', $modelId)->first();

        if (!$model) {
            return response()->json([
                'error' => 'Model not found',
                'message' => "The model '{$modelId}' is not available"
            ], 404);
        }

        if (!$model->is_active) {
            return response()->json([
                'error' => 'Model inactive',
                'message' => "The model '{$modelId}' is currently inactive"
            ], 403);
        }

        $payload = $request->all();
        $apiKey = $request->header('X-API-Key');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if we should queue the request
        $maxQueueSize = config('services.ollama.max_queue_size', 100);
        $queueTimeout = config('services.ollama.queue_timeout', 60);
        $queueSize = Queue::size('ollama-requests');

        if ($queueSize >= $maxQueueSize) {
            return response()->json([
                'error' => 'Too many requests',
                'message' => 'The server is currently handling too many requests. Please try again later.'
            ], 429);
        }

        if ($queueSize > 0) {
            // Queue the request if there are already other requests being processed
            $job = new ProcessOllamaRequest($model, $payload, 'generate', $apiKey, $ipAddress, $userAgent);
            Queue::push($job);

            return response()->json([
                'message' => 'Your request has been queued and will be processed shortly',
                'queue_position' => $queueSize + 1,
                'estimated_time' => $queueSize * $queueTimeout,
            ], 202);
        }

        // Process the request immediately if no queue
        $result = $this->ollamaService->generateCompletion(
            $model,
            $payload,
            $apiKey,
            $ipAddress,
            $userAgent
        );

        if (!$result['success']) {
            return response()->json([
                'error' => 'Generation failed',
                'message' => $result['error'] ?? 'An error occurred during generation'
            ], $result['status'] ?? 500);
        }
        return response()->json($result['data']);
    }

    /**
     * Generate a chat completion using specified model
     *
     * @param Request $request
     * @param string $modelId
     * @return JsonResponse
     */
    public function chat(Request $request, string $modelId): JsonResponse
    {
        $model = OllamaModel::where('model_id', $modelId)->first();

        if (!$model) {
            return response()->json([
                'error' => 'Model not found',
                'message' => "The model '{$modelId}' is not available"
            ], 404);
        }

        if (!$model->is_active) {
            return response()->json([
                'error' => 'Model inactive',
                'message' => "The model '{$modelId}' is currently inactive"
            ], 403);
        }

        $payload = $request->all();
        $apiKey = $request->header('X-API-Key');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if we should queue the request
        $maxQueueSize = config('services.ollama.max_queue_size', 100);
        $queueTimeout = config('services.ollama.queue_timeout', 60);
        $queueSize = Queue::size('ollama-requests');

        if ($queueSize >= $maxQueueSize) {
            return response()->json([
                'error' => 'Too many requests',
                'message' => 'The server is currently handling too many requests. Please try again later.'
            ], 429);
        }

        if ($queueSize > 0) {
            // Queue the request if there are already other requests being processed
            $job = new ProcessOllamaRequest($model, $payload, 'chat', $apiKey, $ipAddress, $userAgent);
            Queue::push($job);

            return response()->json([
                'message' => 'Your request has been queued and will be processed shortly',
                'queue_position' => $queueSize + 1,
                'estimated_time' => $queueSize * $queueTimeout,
            ], 202);
        }

        // Process the request immediately if no queue
        $result = $this->ollamaService->generateChatCompletion(
            $model,
            $payload,
            $apiKey,
            $ipAddress,
            $userAgent
        );

        if (!$result['success']) {
            return response()->json([
                'error' => 'Chat generation failed',
                'message' => $result['error'] ?? 'An error occurred during chat generation'
            ], $result['status'] ?? 500);
        }

        return response()->json($result['data']);
    }

    /**
     * List available models
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function models(Request $request): JsonResponse
    {
        $models = OllamaModel::where('is_active', true)
            ->select('name', 'model_id', 'description')
            ->get();

        return response()->json([
            'models' => $models
        ]);
    }
}
