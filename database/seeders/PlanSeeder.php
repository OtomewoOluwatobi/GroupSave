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
                'description'           => 'Perfect for individuals or small groups just starting their savings journey. Get access to core features with one group and up to 5 members.',
                'price'                 => 0,
                'currency'              => 'GBP',
                'billing'               => 'free_forever',
                'stripe_price_id'       => 'prod_UpixvgB3eXhAGJ',
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
                'description'           => 'Designed for growing communities managing multiple savings groups. Unlimited groups, up to 20 members per group, advanced analytics, and priority support.',
                'price'                 => 999, // £9.99 in pence
                'currency'              => 'GBP',
                'billing'               => 'monthly',
                'stripe_price_id'       => 'price_1TiX2IGVRxfohcvD7b5VkYZZ', // Replace with your Stripe PRICE ID (not product ID)
                'max_groups'            => 9999,
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
                'description'           => 'Enterprise-grade solution for organizations and large networks. Unlimited everything, custom branding, organization-wide dashboards, multi-group oversight, and dedicated account manager.',
                'price'                 => 9999, // £99.99 in pence
                'currency'              => 'GBP',
                'billing'               => 'yearly',
                'stripe_price_id'       => 'price_1TiX2IGVRxfohcvD9ab5cDEFG', // Replace with your Stripe PRICE ID (not product ID)
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
