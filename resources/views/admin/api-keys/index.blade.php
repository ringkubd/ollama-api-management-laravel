@extends('layouts.app')

@section('title', 'API Keys')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">API Keys</h1>
            <a href="{{ route('admin.api-keys.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Create New API Key</a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left">Application</th>
                        <th class="py-3 px-4 text-left">Key Preview</th>
                        <th class="py-3 px-4 text-left">Request Count</th>
                        <th class="py-3 px-4 text-left">Last Used</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($apiKeys as $apiKey)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium">{{ $apiKey->name }}</div>
                                <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($apiKey->description, 50) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ substr($apiKey->key, 0, 8) }}...{{ substr($apiKey->key, -4) }}</code>
                            </td>
                            <td class="py-3 px-4">{{ number_format($apiKey->request_count) }}</td>
                            <td class="py-3 px-4">
                                @if($apiKey->last_used_at)
                                    {{ $apiKey->last_used_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($apiKey->is_active)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.api-keys.show', $apiKey->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('admin.api-keys.edit', $apiKey->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <form action="{{ route('admin.api-keys.destroy', $apiKey->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this API key?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 px-4 text-center text-gray-500">
                                No API keys found. Click "Create New API Key" to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
