<?php

namespace App\Http\Middleware;

use App\Models\Group;
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
     *   - 'invite_member' → checks max_members_per_group limit on the user's plan
     *
     * Usage in routes:
     *   ->middleware('requires.plan')                  // any active plan
     *   ->middleware('requires.plan:create_group')     // plan + group limit check
     */
    public function handle(Request $request, Closure $next, ?string $feature = null): Response
    {
        $user = $request->user();
        $userPlan = $user->activePlan()->with('plan')->first();

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
            $plan = $userPlan->plan;
            $group = Group::find($request->route('id'));
            if ($group && $group->users()->count() >= $plan->max_members_per_group) {
                return response()->json([
                    'message' => "Your {$plan->name} plan allows a maximum of {$plan->max_members_per_group} members per group.",
                    'code' => 'MEMBER_LIMIT_REACHED',
                ], 403);
            }
        }

        return $next($request);
    }
}
