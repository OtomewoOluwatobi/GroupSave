<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates default roles and permissions for the application
     * and sets up initial admin users.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions to ensure fresh seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all available permissions in the system
        $permissions = [
            'create group',    // Permission to create new groups
            'edit group',      // Permission to modify existing groups
            'delete group',    // Permission to remove groups
            'view group',      // Permission to view group details
            'manage members',    // Permission to manage user accounts
            'approve transactions', // Permission to approve financial transactions
            'send notifications'   // Permission to send system notifications
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create and configure basic user role
        $userRole = Role::firstOrCreate(['name' => 'group-admin']);
        $userRole->syncPermissions([
            'create group', 
            'view group',
            'edit group',
            'manage members',
            'approve transactions',
            'send notifications'
        ]); // Basic users get limited permissions

        // Create and configure super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all()); // Super admins get all permissions

        // Create default admin user
        $groupAdmin = User::firstOrCreate(
            ['email' => 'otomewooluwatobi@gmail.com'],
            [
                'name' => 'Otomewo Oluwatobi',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
                'status' => 'active',
            ]
        );
        $groupAdmin->assignRole('group-admin');

        // Create default super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password@1'),
                'phone' => '1234567899',
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole('super-admin');
    }
}
