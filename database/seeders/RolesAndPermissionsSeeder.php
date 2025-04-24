<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator with full access to all features',
            'permissions' => [
                'view_dashboard',
                'view_models',
                'create_models',
                'edit_models',
                'delete_models',
                'sync_models',
                'view_api_keys',
                'create_api_keys',
                'edit_api_keys',
                'delete_api_keys',
                'regenerate_api_keys',
                'view_requests',
                'delete_requests',
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_roles',
                'create_roles',
                'edit_roles',
                'delete_roles',
            ],
        ]);

        $editorRole = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Can manage models and API keys but cannot manage users and roles',
            'permissions' => [
                'view_dashboard',
                'view_models',
                'create_models',
                'edit_models',
                'view_api_keys',
                'create_api_keys',
                'edit_api_keys',
                'view_requests',
            ],
        ]);

        $viewerRole = Role::create([
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only access to dashboard and models',
            'permissions' => [
                'view_dashboard',
                'view_models',
                'view_api_keys',
                'view_requests',
            ],
        ]);

        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        // Assign admin role to the admin user
        $adminUser->roles()->attach($adminRole->id);

        // Optional: Create a demo editor and viewer user
        $editorUser = User::create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $editorUser->roles()->attach($editorRole->id);

        $viewerUser = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $viewerUser->roles()->attach($viewerRole->id);
    }
}
