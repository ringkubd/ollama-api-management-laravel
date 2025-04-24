@extends('layouts.app')

@section('title', 'Edit Model')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Edit Ollama Model</h1>
            <a href="{{ route('admin.models.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">Back to Models</a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.models.update', $model->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Model Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $model->name) }}" class="border border-gray-300 rounded-lg p-2 w-full @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="model_id" class="block text-gray-700 font-medium mb-2">Model ID</label>
                    <input type="text" value="{{ $model->model_id }}" class="border border-gray-200 bg-gray-100 rounded-lg p-2 w-full" readonly>
                    <p class="text-sm text-gray-500 mt-1">Model ID cannot be changed after creation</p>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="border border-gray-300 rounded-lg p-2 w-full @error('description') border-red-500 @enderror">{{ old('description', $model->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="parameters" class="block text-gray-700 font-medium mb-2">Default Parameters (JSON)</label>
                    <textarea name="parameters" id="parameters" rows="5" class="border border-gray-300 rounded-lg p-2 w-full font-mono @error('parameters') border-red-500 @enderror">{{ old('parameters', json_encode($model->parameters, JSON_PRETTY_PRINT)) }}</textarea>
                    <p class="text-sm text-gray-500 mt-1">Enter default parameters in JSON format</p>
                    @error('parameters')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-blue-600" {{ old('is_active', $model->is_active) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">If checked, this model will be available for API requests</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Update Model
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
