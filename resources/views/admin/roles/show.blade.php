@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold">Role Details: {{ $role->name }}</h1>
            <div class="space-x-2">
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
                    Back to Roles
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Role Details -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Role Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-700">Name</h3>
                            <p class="text-gray-800">{{ $role->name }}</p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-700">Slug</h3>
                            <p class="text-gray-800">{{ $role->slug }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="font-semibold text-gray-700">Description</h3>
                            <p class="text-gray-800">{{ $role->description ?? 'No description provided' }}</p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-700">Created</h3>
                            <p class="text-gray-800">{{ $role->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-700">Last Updated</h3>
                            <p class="text-gray-800">{{ $role->updated_at->format('M d, Y H:i') }}</p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-700">Users with this role</h3>
                            <p class="text-gray-800">{{ $role->users_count }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h2 class="text-xl font-semibold mb-4">Permissions</h2>

                    @if(!empty($role->permissions))
                        <div class="grid gap-4">
                            @foreach($groupedPermissions as $group => $permissions)
                                <div class="mb-3">
                                    <h3 class="font-medium text-gray-800 capitalize mb-2">{{ $group }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($permissions as $permission)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded">
                                                {{ $availablePermissions[$group][$permission] ?? $permission }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No permissions assigned to this role.</p>
                    @endif
                </div>
            </div>

            <!-- Users with this role -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Users with this Role</h2>

                    @if($users->count() > 0)
                        <div class="space-y-3">
                            @foreach($users as $user)
                                <div class="border-b pb-3 last:border-b-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 text-sm hover:underline">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($users->hasPages())
                            <div class="mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500 italic">No users have this role assigned.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
