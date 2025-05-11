<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear the permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'create group',
            'edit group',
            'delete group',
            'view group',
            'join groups',
            'leave groups',
            'manage group members',
            'approve transactions',
            'send notifications',
            'make payments',
            'view payments',
            'manage payments',
            'manage users',
            'view users',
            'delete users',
            'edit profile',
            'view profile',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign permissions
        $roles = [
            'group-admin' => [
                'create group',
                'edit group',
                'view group',
                'manage group members',
                'approve transactions',
                'send notifications',
                'make payments',
            ],
            'user' => [
                'join groups',
                'leave groups',
                'view group',
                'make payments',
                'view payments',
                'edit profile',
                'view profile',
            ],
            'super-admin' => Permission::pluck('name')->toArray(), // All permissions
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Create default users and assign roles
        $defaultUsers = [
            [
                'name' => 'Otomewo Oluwatobi',
                'email' => 'otomewooluwatobi@gmail.com',
                'password' => bcrypt('password'),
                'mobile' => '1234567890',
                'status' => 'active',
                'role' => 'group-admin',
            ],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password@1'),
                'mobile' => '1234567899',
                'status' => 'active',
                'role' => 'super-admin',
            ],
        ];

        foreach ($defaultUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                collect($userData)->except('role')->toArray()
            );
            $user->assignRole($userData['role']);
        }
    }
}
