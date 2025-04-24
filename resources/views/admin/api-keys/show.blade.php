@extends('layouts.app')

@section('title', 'API Key Details')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">API Key: {{ $apiKey->name }}</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.api-keys.edit', $apiKey->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.api-keys.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">Back to API Keys</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold mb-4">API Key Information</h2>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        This is your complete API key. For security, it's only shown once.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 bg-gray-100 p-3 rounded-lg break-all">
                            <code class="text-sm font-mono">{{ $apiKey->key }}</code>
                        </div>

                        <div class="flex justify-end">
                            <form action="{{ route('admin.api-keys.regenerate', $apiKey->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to regenerate this API key? The old key will stop working immediately.');">
                                @csrf
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white py-1 px-4 rounded text-sm">
                                    Regenerate Key
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600 text-sm">Name</p>
                            <p class="font-medium">{{ $apiKey->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p>
                                @if($apiKey->is_active)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Created</p>
                            <p>{{ $apiKey->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Last Used</p>
                            <p>
                                @if($apiKey->last_used_at)
                                    {{ $apiKey->last_used_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($apiKey->description)
                        <div class="mb-6">
                            <h3 class="font-medium mb-2">Description</h3>
                            <p class="text-gray-800">{{ $apiKey->description }}</p>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-medium mb-2">Allowed Models</h3>
                        @if(!empty($apiKey->allowed_models) && count($apiKey->allowed_models) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach(\App\Models\OllamaModel::whereIn('id', $apiKey->allowed_models)->get() as $model)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">{{ $model->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 italic">All models (no restrictions)</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Requests</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">Time</th>
                                    <th class="py-2 px-4 text-left">Model</th>
                                    <th class="py-2 px-4 text-left">Endpoint</th>
                                    <th class="py-2 px-4 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRequests as $request)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-4">{{ $request->created_at->diffForHumans() }}</td>
                                        <td class="py-2 px-4">{{ $request->ollamaModel->name }}</td>
                                        <td class="py-2 px-4">{{ $request->endpoint }}</td>
                                        <td class="py-2 px-4">
                                            @if($request->status === 'completed')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Completed</span>
                                            @elseif($request->status === 'failed')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Failed</span>
                                            @elseif($request->status === 'processing')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">Processing</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">No requests found for this API key yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Usage Statistics</h2>
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($apiKey->request_count) }}</div>
                        <p class="text-gray-600">Total Requests</p>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-medium mb-2">API Usage Examples</h3>
                        <p class="text-sm mb-2">Include this key in all your API requests:</p>
                        <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto mb-4"><code>X-API-Key: {{ $apiKey->key }}</code></pre>

                        <p class="text-sm mb-2">Example cURL request:</p>
                        <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto"><code>curl -X POST \
    -H "Content-Type: application/json" \
    -H "X-API-Key: {{ $apiKey->key }}" \
    -d '{"prompt": "Hello, how are you?"}' \
    https://your-domain.com/api/v1/generate/llama2</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
