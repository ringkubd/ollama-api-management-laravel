<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if API key validation is enabled
        if (!config('services.ollama.require_api_key', true)) {
            return $next($request);
        }

        // Get the API key from the request header
        $apiKey = $request->header('X-API-Key');

        // If no API key is provided
        if (!$apiKey) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'API key is required'
            ], 401);
        }

        // Validate the API key against the allowed keys
        $validKeys = config('services.ollama.api_keys', []);

        if (empty($validKeys) || in_array($apiKey, $validKeys)) {
            // Store the API key in the request for logging
            $request->apiKey = $apiKey;
            return $next($request);
        }

        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Invalid API key'
        ], 401);
    }
}
