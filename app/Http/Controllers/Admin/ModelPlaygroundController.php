<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OllamaModel;
use App\Services\OllamaService;
use App\Services\OllamaCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModelPlaygroundController extends Controller
{
    protected $ollamaService;
    protected $completionService;

    public function __construct(OllamaService $ollamaService, OllamaCompletionService $completionService)
    {
        $this->ollamaService = $ollamaService;
        $this->completionService = $completionService;
    }

    /**
     * Display the model playground interface.
     */
    public function index()
    {
        $models = OllamaModel::where('is_active', true)->orderBy('name')->get();

        return view('admin.playground.index', compact('models'));
    }

    /**
     * Process a chat request in the playground.
     */
    public function chat(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'model_id' => 'required|exists:ollama_models,model_id',
            'messages' => 'required|array|min:1',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:1|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Fetch the actual OllamaModel object from the database
            $model = OllamaModel::where('model_id', $request->model_id)->firstOrFail();

            // Use the new completion service that properly handles non-streaming requests
            $response = $this->completionService->generateChatCompletion(
                $model,
                $request->messages,
                $request->temperature ?? 0.7,
                $request->max_tokens ?? 1024
            );

            // Log the response for debugging
            Log::debug('Ollama Chat Response', ['response' => $response]);

            // Format the response to match what the frontend expects
            return response()->json([
                'id' => uniqid('playground-'),
                'object' => 'chat.completion',
                'created' => time(),
                'model' => $model->model_id,
                'choices' => [
                    [
                        'index' => 0,
                        'message' => [
                            'role' => 'assistant',
                            'content' => $response['message']['content'] ?? 'No response received'
                        ],
                        'finish_reason' => 'stop'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Chat playground error: ' . $e->getMessage(), [
                'model_id' => $request->model_id,
                'exception' => $e,
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_trace' => $e->getTraceAsString(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_code' => $e->getCode()
            ], 500);
        }
    }

    /**
     * Process a text generation request in the playground.
     */
    public function generate(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'model_id' => 'required|exists:ollama_models,model_id',
            'prompt' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:1|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Fetch the actual OllamaModel object
            $model = OllamaModel::where('model_id', $request->model_id)->firstOrFail();

            // Use the new completion service that properly handles non-streaming requests
            $response = $this->completionService->generateCompletion(
                $model,
                $request->prompt,
                $request->max_tokens ?? 1024,
                $request->temperature ?? 0.7
            );

            // Log the response for debugging
            Log::debug('Ollama Text Generation Response', ['response' => $response]);

            // Format the response to match what the frontend expects
            return response()->json([
                'id' => uniqid('playground-'),
                'object' => 'text.completion',
                'created' => time(),
                'model' => $model->model_id,
                'choices' => [
                    [
                        'index' => 0,
                        'text' => $response['response'] ?? 'No response received',
                        'finish_reason' => 'stop'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Generate playground error: ' . $e->getMessage(), [
                'model_id' => $request->model_id,
                'exception' => $e,
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_trace' => $e->getTraceAsString(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_code' => $e->getCode()
            ], 500);
        }
    }
}
