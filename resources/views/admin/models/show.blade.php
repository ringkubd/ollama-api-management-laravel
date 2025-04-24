@extends('layouts.app')

@section('title', 'Model Details')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Model Details: {{ $model->name }}</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.models.edit', $model->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.models.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">Back to Models</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600 text-sm">Name</p>
                                <p class="font-medium">{{ $model->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Model ID</p>
                                <p class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $model->model_id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Status</p>
                                <p>
                                    @if($model->is_active)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Created</p>
                                <p>{{ $model->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Description</h2>
                        <p class="text-gray-800">{{ $model->description ?: 'No description provided.' }}</p>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-2">Default Parameters</h2>
                        @if($model->parameters)
                            <pre class="bg-gray-100 p-4 rounded-lg overflow-x-auto"><code>{{ json_encode($model->parameters, JSON_PRETTY_PRINT) }}</code></pre>
                        @else
                            <p class="text-gray-600 italic">No default parameters specified.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Usage Statistics</h2>
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($model->request_count) }}</div>
                        <p class="text-gray-600">Total Requests</p>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-medium mb-2">API Endpoints</h3>
                        <div class="mb-2">
                            <p class="text-sm font-medium">Text Generation</p>
                            <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto"><code>POST /api/v1/generate/{{ $model->model_id }}</code></pre>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Chat Completions</p>
                            <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto"><code>POST /api/v1/chat/{{ $model->model_id }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
