@extends('layouts.app')

@section('title', 'Manage Roles')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Roles</h1>
            <a href="{{ route('admin.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                Add New Role
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Slug</th>
                            <th class="py-3 px-4 text-left">Description</th>
                            <th class="py-3 px-4 text-left">Users</th>
                            <th class="py-3 px-4 text-left">Permissions</th>
                            <th class="py-3 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium">{{ $role->name }}</td>
                                <td class="py-3 px-4">{{ $role->slug }}</td>
                                <td class="py-3 px-4">{{ $role->description }}</td>
                                <td class="py-3 px-4">{{ $role->users_count }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions ?? [] as $permission)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                {{ $permission }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>

                                        @if($role->slug !== 'admin')
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
