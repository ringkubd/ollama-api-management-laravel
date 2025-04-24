@extends('layouts.app')

@section('title', 'Models')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Ollama Models</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.models.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Add Model</a>
                <form action="{{ route('admin.models.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Sync Models</button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="py-3 px-4 text-left">Name</th>
                        <th class="py-3 px-4 text-left">Model ID</th>
                        <th class="py-3 px-4 text-left">Description</th>
                        <th class="py-3 px-4 text-left">Request Count</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($models as $model)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $model->name }}</td>
                            <td class="py-3 px-4"><code class="bg-gray-100 px-1 py-1 rounded text-sm">{{ $model->model_id }}</code></td>
                            <td class="py-3 px-4">{{ \Illuminate\Support\Str::limit($model->description, 50) }}</td>
                            <td class="py-3 px-4">{{ number_format($model->request_count) }}</td>
                            <td class="py-3 px-4">
                                @if($model->is_active)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.models.edit', $model->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    <form action="{{ route('admin.models.destroy', $model->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this model?');" class="inline">
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
                                No models found. Click "Sync Models" to fetch models from Ollama API.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
