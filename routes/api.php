<?php

use App\Http\Controllers\API\OllamaApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Ollama API Routes
Route::prefix('v1')->middleware(['App\Http\Middleware\ValidateApiKey'])->group(function () {
    // List available models
    Route::get('/models', [OllamaApiController::class, 'models']);

    // Text generation endpoint
    Route::post('/generate/{modelId}', [OllamaApiController::class, 'generate']);

    // Chat completion endpoint
    Route::post('/chat/{modelId}', [OllamaApiController::class, 'chat']);
});
