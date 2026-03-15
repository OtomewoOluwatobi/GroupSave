<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class RequiresPlan
{
    /**
     * Ensure the authenticated user has an active plan.
     *
     * Optional $feature parameter enables feature-specific limit checks:
     *   - 'create_group'  → checks max_groups limit on the user's plan
     *
     * Usage in routes:
     *   ->middleware('requires.plan')                  // any active plan
     *   ->middleware('requires.plan:create_group')     // plan + group limit check
     */
    public function handle(Request $request, Closure $next, ?string $feature = null): Response
    {
        $user = $request->user();
        $userPlan = $user->activePlan;

        if (!$userPlan || !$userPlan->plan) {
            return response()->json([
                'message' => 'You need an active plan to access this feature.',
                'code' => 'NO_ACTIVE_PLAN',
            ], 403);
        }

        if ($userPlan->expires_at && $userPlan->expires_at->isPast()) {
            return response()->json([
                'message' => 'Your plan has expired. Please renew to continue.',
                'code' => 'PLAN_EXPIRED',
            ], 403);
        }

        if ($feature === 'create_group' && !$user->canCreateGroup()) {
            $plan = $userPlan->plan;
            return response()->json([
                'message' => "Your {$plan->name} plan allows a maximum of {$plan->max_groups} group(s).",
                'code' => 'GROUP_LIMIT_REACHED',
            ], 403);
        }

        if ($feature === 'invite_member') {
            $maxMembers = $userPlan->plan->max_members_per_group;
            $current = Group::find($request->route('id'))?->members()->count() ?? 0;
            if ($current >= $maxMembers) {
                return response()->json([
                    'message' => "Your plan allows max {$maxMembers} members per group.",
                    'code' => 'MEMBER_LIMIT_REACHED',
                ], 403);
            }
        }

        if ($feature === 'create_group' && $user->groups()->count() >= $userPlan->plan->max_groups) {
            return response()->json([
                'message' => "Your plan allows a maximum of {$userPlan->plan->max_groups} group(s).",
                'code' => 'GROUP_LIMIT_REACHED',
            ], 403);
        }

        if ($feature === 'invite_member') {
            $group = Group::find($request->route('id'));
            if ($group && $group->members()->count() >= $userPlan->plan->max_members_per_group) {
                return response()->json([
                    'message' => "Your plan allows a maximum of {$userPlan->plan->max_members_per_group} members per group.",
                    'code' => 'MEMBER_LIMIT_REACHED',
                ], 403);
            }
        }

        return $next($request);
    }
}
