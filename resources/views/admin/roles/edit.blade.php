@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Edit Role: {{ $role->name }}</h1>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
                Back to Roles
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Role Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required autofocus
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                    <p class="text-gray-500 text-xs mt-1">This will update the role's slug.</p>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea id="description" name="description" rows="2"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Permissions</label>

                    @foreach($availablePermissions as $group => $permissions)
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700 capitalize mb-2">{{ $group }} Permissions</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 p-3 bg-gray-50 rounded">
                                @foreach($permissions as $key => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" id="permission_{{ $key }}"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            @if(
                                                (old('permissions') && in_array($key, old('permissions'))) ||
                                                (!old('permissions') && isset($role->permissions) && in_array($key, $role->permissions))
                                            ) checked @endif>
                                        <label for="permission_{{ $key }}" class="ml-2 block text-sm text-gray-900">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @error('permissions')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
