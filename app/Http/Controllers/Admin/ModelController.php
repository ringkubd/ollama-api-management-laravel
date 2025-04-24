<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OllamaModel;
use App\Services\OllamaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModelController extends Controller
{
    protected OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    /**
     * Display a listing of the models.
     */
    public function index()
    {
        $models = OllamaModel::orderBy('name')->get();

        return view('admin.models.index', compact('models'));
    }

    /**
     * Show the form for creating a new model.
     */
    public function create()
    {
        return view('admin.models.create');
    }

    /**
     * Store a newly created model in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'model_id' => 'required|string|max:255|unique:ollama_models',
            'description' => 'nullable|string',
            'parameters' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.models.create')
                ->withErrors($validator)
                ->withInput();
        }

        $parameters = null;
        if ($request->filled('parameters')) {
            $parameters = json_decode($request->parameters, true);
        }

        OllamaModel::create([
            'name' => $request->name,
            'model_id' => $request->model_id,
            'description' => $request->description,
            'parameters' => $parameters,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.models.index')
            ->with('success', 'Model created successfully.');
    }

    /**
     * Display the specified model.
     */
    public function show(OllamaModel $model)
    {
        return view('admin.models.show', compact('model'));
    }

    /**
     * Show the form for editing the specified model.
     */
    public function edit(OllamaModel $model)
    {
        return view('admin.models.edit', compact('model'));
    }

    /**
     * Update the specified model in storage.
     */
    public function update(Request $request, OllamaModel $model)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parameters' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.models.edit', $model->id)
                ->withErrors($validator)
                ->withInput();
        }

        $parameters = null;
        if ($request->filled('parameters')) {
            $parameters = json_decode($request->parameters, true);
        }

        $model->update([
            'name' => $request->name,
            'description' => $request->description,
            'parameters' => $parameters,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.models.index')
            ->with('success', 'Model updated successfully.');
    }

    /**
     * Remove the specified model from storage.
     */
    public function destroy(OllamaModel $model)
    {
        $model->delete();

        return redirect()->route('admin.models.index')
            ->with('success', 'Model deleted successfully.');
    }

    /**
     * Sync models from Ollama API.
     */
    public function syncModels()
    {
        $count = $this->ollamaService->syncModels();

        return redirect()->route('admin.models.index')
            ->with('success', "$count models synchronized successfully from Ollama API.");
    }
}
