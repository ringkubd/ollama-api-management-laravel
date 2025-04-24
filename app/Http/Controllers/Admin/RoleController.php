<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        // Define common permissions for the system
        $availablePermissions = $this->getAvailablePermissions();

        return view('admin.roles.create', compact('availablePermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.roles.create')
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->loadCount('users');
        $users = $role->users()->take(10)->get();

        return view('admin.roles.show', compact('role', 'users'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $availablePermissions = $this->getAvailablePermissions();

        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.roles.edit', $role->id)
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // First check if this is the admin role
        if ($role->slug === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'The admin role cannot be deleted.');
        }

        // Detach all users from this role before deletion
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Get the list of available permissions in the system.
     */
    private function getAvailablePermissions(): array
    {
        // Define permissions by feature group
        return [
            'dashboard' => [
                'view_dashboard' => 'View Dashboard',
            ],
            'models' => [
                'view_models' => 'View Models',
                'create_models' => 'Create Models',
                'edit_models' => 'Edit Models',
                'delete_models' => 'Delete Models',
                'sync_models' => 'Sync Models from Ollama',
            ],
            'api_keys' => [
                'view_api_keys' => 'View API Keys',
                'create_api_keys' => 'Create API Keys',
                'edit_api_keys' => 'Edit API Keys',
                'delete_api_keys' => 'Delete API Keys',
                'regenerate_api_keys' => 'Regenerate API Keys',
            ],
            'requests' => [
                'view_requests' => 'View API Requests',
                'delete_requests' => 'Delete API Requests',
            ],
            'users' => [
                'view_users' => 'View Users',
                'create_users' => 'Create Users',
                'edit_users' => 'Edit Users',
                'delete_users' => 'Delete Users',
            ],
            'roles' => [
                'view_roles' => 'View Roles',
                'create_roles' => 'Create Roles',
                'edit_roles' => 'Edit Roles',
                'delete_roles' => 'Delete Roles',
            ],
        ];
    }
}
