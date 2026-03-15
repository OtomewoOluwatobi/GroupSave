<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // Permissions
        // ----------------------------------------------------------------
        $permissions = [
            'create-group',
            'join-group',
            'export-reports',
            'advanced-analytics',
            'custom-branding',
            'multi-group-oversight',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        // ----------------------------------------------------------------
        // Roles & permission assignments
        // ----------------------------------------------------------------
        $starter = Role::firstOrCreate(['name' => 'starter', 'guard_name' => 'api']);
        $starter->syncPermissions(['create-group', 'join-group']);

        $growth = Role::firstOrCreate(['name' => 'growth', 'guard_name' => 'api']);
        $growth->syncPermissions([
            'create-group', 'join-group',
            'export-reports', 'advanced-analytics',
        ]);

        $enterprise = Role::firstOrCreate(['name' => 'enterprise', 'guard_name' => 'api']);
        $enterprise->syncPermissions([
            'create-group', 'join-group',
            'export-reports', 'advanced-analytics',
            'custom-branding', 'multi-group-oversight',
        ]);

        // Admin role gets everything
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions($permissions);

        // ----------------------------------------------------------------
        // Plans
        // ----------------------------------------------------------------
        $plans = [
            [
                'name'                  => 'Starter',
                'slug'                  => 'starter',
                'tagline'               => 'Begin Growing',
                'price'                 => 0,
                'currency'              => 'GBP',
                'billing'               => 'free_forever',
                'max_groups'            => 1,
                'max_members_per_group' => 5,
                'features'              => [
                    '1 active savings group',
                    'Up to 5 members',
                    'Basic contribution tracking',
                    'Shared transparent ledger',
                    'Email notifications',
                    'Reward-based unlocks',
                ],
                'built_for' => null,
                'is_active' => true,
            ],
            [
                'name'                  => 'Growth',
                'slug'                  => 'growth',
                'tagline'               => 'Scale Your Savings',
                'price'                 => 499,
                'currency'              => 'GBP',
                'billing'               => 'monthly',
                'max_groups'            => 9999, // unlimited
                'max_members_per_group' => 20,
                'features'              => [
                    'Everything in Starter',
                    'Unlimited groups',
                    'Up to 20 members per group',
                    'Smart automated reminders',
                    'Advanced analytics dashboard',
                    'Detailed trust score insights',
                    'Export reports (PDF / CSV)',
                    'Zero ads',
                    'Priority support',
                ],
                'built_for' => null,
                'is_active' => true,
            ],
            [
                'name'                  => 'Enterprise',
                'slug'                  => 'enterprise',
                'tagline'               => 'Lead Your Community',
                'price'                 => 19900,
                'currency'              => 'GBP',
                'billing'               => 'yearly',
                'max_groups'            => 9999,
                'max_members_per_group' => 9999,
                'features'              => [
                    'Everything in Growth',
                    'Unlimited members',
                    'Custom branding',
                    'Organisation-wide dashboard',
                    'Multi-group oversight tools',
                    'Dedicated account manager',
                ],
                'built_for' => [
                    'Community associations',
                    'Churches & cultural organisations',
                    'Migrant support groups',
                    'Savings networks & cooperatives',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $data) {
            Plan::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
