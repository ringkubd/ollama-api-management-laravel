<?php

namespace App\Jobs;

use App\Services\OllamaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ProcessNotesAssistanceRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $noteContent;
    protected $requestType; // 'suggestion', 'correction', 'summary', etc.
    protected $modelId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $noteContent, string $requestType, string $modelId = 'llama2')
    {
        $this->userId = $userId;
        $this->noteContent = $noteContent;
        $this->requestType = $requestType;
        $this->modelId = $modelId;
    }

    /**
     * Execute the job.
     */
    public function handle(OllamaService $ollamaService): void
    {
        try {
            // Build prompt based on request type
            $prompt = $this->buildPrompt();

            // Call Ollama API through your service
            $response = $ollamaService->generateText($this->modelId, $prompt, 200);

            if (isset($response['choices'][0]['text'])) {
                $generatedContent = $response['choices'][0]['text'];

                // Send the result through Soketi/Pusher
                broadcast(new \App\Events\NoteAssistanceGenerated(
                    $this->userId,
                    $this->requestType,
                    $generatedContent
                ));

                // Log successful completion
                Log::info('Notes assistance generated successfully', [
                    'user_id' => $this->userId,
                    'type' => $this->requestType,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing notes assistance request', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
            ]);
        }
    }

    /**
     * Build appropriate prompt based on request type
     */
    private function buildPrompt(): string
    {
        return match ($this->requestType) {
            'suggestion' => "Continue this note with relevant suggestions: {$this->noteContent}",
            'correction' => "Correct any grammar or spelling issues in this text, but keep the meaning: {$this->noteContent}",
            'summary' => "Provide a concise summary of the following notes: {$this->noteContent}",
            'tagging' => "Suggest 3-5 relevant tags for this note content: {$this->noteContent}",
            default => "Analyze this note and provide helpful insights: {$this->noteContent}"
        };
    }
}
