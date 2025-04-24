@extends('layouts.app')

@section('title', 'Create API Key')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Create New API Key</h1>
            <a href="{{ route('admin.api-keys.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">Back to API Keys</a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.api-keys.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Application Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="border border-gray-300 rounded-lg p-2 w-full @error('name') border-red-500 @enderror" placeholder="e.g. Mobile App" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="border border-gray-300 rounded-lg p-2 w-full @error('description') border-red-500 @enderror" placeholder="Purpose of this API key">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Allowed Models</label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                        @foreach($models as $model)
                            <div class="mb-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="allowed_models[]" value="{{ $model->id }}" class="form-checkbox h-5 w-5 text-blue-600" {{ in_array($model->id, old('allowed_models', [])) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $model->name }} <span class="text-gray-500 text-sm">({{ $model->model_id }})</span></span>
                                </label>
                            </div>
                        @endforeach

                        @if($models->isEmpty())
                            <p class="text-gray-500 italic">No models available. Please create models first.</p>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Leave unchecked to allow access to all models.</p>
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-blue-600" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">If unchecked, this API key will not work for any requests.</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Generate API Key
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
