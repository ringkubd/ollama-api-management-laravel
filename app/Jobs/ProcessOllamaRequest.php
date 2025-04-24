<?php

namespace App\Jobs;

use App\Models\OllamaModel;
use App\Services\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOllamaRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * The model to use for the request.
     */
    protected OllamaModel $model;

    /**
     * The payload for the request.
     */
    protected array $payload;

    /**
     * The type of request: 'generate' or 'chat'.
     */
    protected string $requestType;

    /**
     * The API key for the request.
     */
    protected ?string $apiKey;

    /**
     * The IP address of the requester.
     */
    protected string $ipAddress;

    /**
     * The user agent of the requester.
     */
    protected ?string $userAgent;

    /**
     * Create a new job instance.
     */
    public function __construct(
        OllamaModel $model,
        array $payload,
        string $requestType,
        ?string $apiKey = null,
        string $ipAddress = '',
        ?string $userAgent = null
    ) {
        $this->model = $model;
        $this->payload = $payload;
        $this->requestType = $requestType;
        $this->apiKey = $apiKey;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;

        // Set queue to process Ollama requests
        $this->onQueue('ollama-requests');
    }

    /**
     * Execute the job.
     */
    public function handle(OllamaService $ollamaService): void
    {
        Log::info('Processing Ollama request', [
            'model' => $this->model->model_id,
            'request_type' => $this->requestType,
        ]);

        try {
            if ($this->requestType === 'chat') {
                $ollamaService->generateChatCompletion(
                    $this->model,
                    $this->payload,
                    $this->apiKey,
                    $this->ipAddress,
                    $this->userAgent
                );
            } else {
                $ollamaService->generateCompletion(
                    $this->model,
                    $this->payload,
                    $this->apiKey,
                    $this->ipAddress,
                    $this->userAgent
                );
            }
        } catch (\Exception $e) {
            Log::error('Error processing Ollama request', [
                'model' => $this->model->model_id,
                'request_type' => $this->requestType,
                'error' => $e->getMessage(),
            ]);

            // Retry the job or mark it as failed
            $this->fail($e);
        }
    }
}
