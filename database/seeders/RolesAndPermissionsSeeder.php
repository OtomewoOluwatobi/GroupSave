<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'create group',
            'edit group',
            'delete group',
            'view group',
            'manage users',
            'approve transactions',
            'send notifications'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions); // Admin has all permissions

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['create group', 'view group']); // Users have limited permissions

        // Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign Super Admin all permissions dynamically
        $superAdminRole->syncPermissions(Permission::all());

        // Optionally create a default admin user
        $admin = \App\Models\User::firstOrCreate([
            'email' => 'otomewooluwatobi@gmail.com',
        ], [
            'name' => 'Otomewo Oluwatobi',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'status' => 'active',
        ]);

        $admin->assignRole('admin');

        // Optionally create a default super admin user
        $superAdmin = \App\Models\User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password@1'),
            'phone' => '1234567899',
            'status' => 'active',
        ]);

        $superAdmin->assignRole('super-admin');
    }
}
