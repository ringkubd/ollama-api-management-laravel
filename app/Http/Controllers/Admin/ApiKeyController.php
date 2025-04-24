<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\OllamaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of API keys.
     */
    public function index()
    {
        $apiKeys = ApiKey::orderBy('created_at', 'desc')->get();

        return view('admin.api-keys.index', compact('apiKeys'));
    }

    /**
     * Show the form for creating a new API key.
     */
    public function create()
    {
        $models = OllamaModel::where('is_active', true)->get();

        return view('admin.api-keys.create', compact('models'));
    }

    /**
     * Store a newly created API key in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allowed_models' => 'nullable|array',
            'allowed_models.*' => 'exists:ollama_models,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.api-keys.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a unique API key
        $key = ApiKey::generateKey();

        $apiKey = ApiKey::create([
            'name' => $request->name,
            'key' => $key,
            'description' => $request->description,
            'allowed_models' => $request->allowed_models,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.api-keys.show', $apiKey->id)
            ->with('success', 'API key created successfully.');
    }

    /**
     * Display the specified API key.
     */
    public function show(ApiKey $apiKey)
    {
        $recentRequests = $apiKey->apiRequests()
            ->with('ollamaModel')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.api-keys.show', compact('apiKey', 'recentRequests'));
    }

    /**
     * Show the form for editing the specified API key.
     */
    public function edit(ApiKey $apiKey)
    {
        $models = OllamaModel::where('is_active', true)->get();

        return view('admin.api-keys.edit', compact('apiKey', 'models'));
    }

    /**
     * Update the specified API key in storage.
     */
    public function update(Request $request, ApiKey $apiKey)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allowed_models' => 'nullable|array',
            'allowed_models.*' => 'exists:ollama_models,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.api-keys.edit', $apiKey->id)
                ->withErrors($validator)
                ->withInput();
        }

        $apiKey->update([
            'name' => $request->name,
            'description' => $request->description,
            'allowed_models' => $request->allowed_models,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key updated successfully.');
    }

    /**
     * Remove the specified API key from storage.
     */
    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API key deleted successfully.');
    }

    /**
     * Regenerate the API key.
     */
    public function regenerate(ApiKey $apiKey)
    {
        $apiKey->update([
            'key' => ApiKey::generateKey(),
        ]);

        return redirect()->route('admin.api-keys.show', $apiKey->id)
            ->with('success', 'API key regenerated successfully.');
    }
}
