<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Models\Group;
use App\Models\User;
use App\Notifications\GroupInvitationAcceptedNotification;
use App\Notifications\GroupInvitationNotification;
use App\Notifications\GroupJoinApprovedNotification;
use App\Notifications\GroupJoinRequestNotification;
use App\Notifications\GroupJoinRejectedNotification;
use App\Notifications\GroupMemberRemovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'total_users' => 'required|integer|min:2|max:300',
            'target_amount' => 'required|numeric|min:0',
            'expected_start_date' => 'required|date|after:today',
            'payment_out_day' => 'required|integer|min:1|max:31',
            'members_emails' => 'required|array',  // Changed from members_emails
            'members_emails.*' => 'required|email'  // Changed from members_emails.*
        ]);

        $payableAmount = (float) $validated['target_amount'] / (int) $validated['total_users'];
        $expectedEndDate = \Carbon\Carbon::parse($validated['expected_start_date'])
            ->addMonths($validated['total_users'])
            ->format('Y-m-d');

        try {
            $group = DB::transaction(function () use ($validated, $payableAmount, $expectedEndDate) {
                $group = Group::create([
                    'title' => $validated['title'],
                    'total_users' => (int) $validated['total_users'],
                    'target_amount' => (float) $validated['target_amount'],
                    'payable_amount' => $payableAmount,
                    'expected_start_date' => $validated['expected_start_date'],
                    'expected_end_date' => $expectedEndDate,
                    'payment_out_day' => (int) $validated['payment_out_day'],
                    'owner_id' => Auth::id(),
                    'status' => 'active'
                ]);

                $group->users()->attach(Auth::id(), [
                    'role' => 'admin',
                    'is_active' => true
                ]);

                $this->inviteMembers($group, $validated['members_emails']);

                return $group;
            });

            // Dispatch event after successful group creation
            event(new GroupCreated($group));

            return response()->json([
                'message' => 'Group created successfully',
                'data' => $group
            ], 201);
        } catch (\Exception $e) {
            Log::error('Group creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'message' => 'Group creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle member invitations
     *
     * @param Group $group
     * @param array $emails
     * @return void
     */
    private function inviteMembers(Group $group, array $emails)
    {
        // Exclude the creator's email
        $creatorEmail = Auth::user()->email;
        $memberEmails = array_filter($emails, fn($email) => $email !== $creatorEmail);

        foreach ($memberEmails as $email) {
            try {
                DB::transaction(function () use ($group, $email) {
                    $generatedPassword = Str::random(10);

                    // Create user if they don't already exist
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => strstr($email, '@', true),
                            'password' => Hash::make($generatedPassword)
                        ]
                    );

                    // Check if user is already a member of the group
                    if (!$group->users()->where('user_id', $user->id)->exists()) {
                        $group->users()->attach($user->id, [
                            'role' => 'member',
                            'is_active' => false
                        ]);

                        $this->sendInvitationEmail($group, $user, $generatedPassword);

                        Log::info('User invited to group', [
                            'user_id' => $user->id,
                            'group_id' => $group->id,
                            'email' => $email
                        ]);
                    }
                });
            } catch (\Exception $e) {
                Log::error('Failed to invite user', [
                    'email' => $email,
                    'group_id' => $group->id,
                    'error' => $e->getMessage()
                ]);

                // Continue with next email instead of breaking the entire process
                continue;
            }
        }
    }

    /**
     * Send invitation email to user
     *
     * @param Group $group
     * @param User $user
     * @return void
     */
    private function sendInvitationEmail(Group $group, User $user, $generatedPassword)
    {
        // Double check to ensure we're not sending to the creator
        if ($user->id !== Auth::id()) {
            $user->notify(new GroupInvitationNotification($group, $user, $generatedPassword));
        }
    }

    /**
     * Accept group invitation
     *
     * @param int $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptInvitation($groupId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $user = Auth::user();
        if (!$group->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Invitation not found'], 404);
        }

        $pivot = $group->users()->where('user_id', $user->id)->first()->pivot;
        if ($pivot->is_active) {
            return response()->json(['message' => 'Invitation already accepted'], 200);
        }

        try {
            $group->users()->updateExistingPivot($user->id, ['is_active' => true]);

            // Send notification to group admin/owner
            $groupOwner = User::find($group->owner_id);
            if ($groupOwner) {
                $groupOwner->notify(new GroupInvitationAcceptedNotification($group, $user));
            }

            return response()->json([
                'message' => 'Invitation accepted successfully',
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to accept invitation', [
                'user_id' => $user->id,
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to accept invitation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send join request to group admin
     *
     * @param int $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendJoinRequest($groupId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $user = Auth::user();

        // Check if user is already a member
        if ($group->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'You are already a member of this group'], 400);
        }

        // Check if join request already exists
        $existingRequest = DB::table('group_join_requests')
            ->where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'You have already sent a join request to this group'], 400);
        }

        try {
            DB::transaction(function () use ($group, $user) {
                // Create join request
                DB::table('group_join_requests')->insert([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Notify group owner/admin about the join request
                $groupOwner = User::find($group->owner_id);
                if ($groupOwner) {
                    $groupOwner->notify(new GroupJoinRequestNotification(
                        $group->id,
                        $group->title,
                        $user->id,
                        $user->name
                    ));
                }

                Log::info('User sent join request to group', [
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                ]);
            });

            return response()->json([
                'message' => 'Join request sent successfully',
                'data' => [
                    'group_id' => $group->id,
                    'group_title' => $group->title,
                    'status' => 'pending'
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to send join request', [
                'user_id' => $user->id,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to send join request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending join requests for a group (admin only)
     *
     * @param int $groupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingJoinRequests($groupId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Check if user is group admin
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Only group admin can view join requests'], 403);
        }

        try {
            $joinRequests = DB::table('group_join_requests')
                ->where('group_id', $groupId)
                ->where('status', 'pending')
                ->join('users', 'group_join_requests.user_id', '=', 'users.id')
                ->select('group_join_requests.id', 'users.id as user_id', 'users.name', 'users.email', 'group_join_requests.created_at')
                ->orderBy('group_join_requests.created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Join requests retrieved successfully',
                'data' => $joinRequests,
                'count' => $joinRequests->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve join requests', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to retrieve join requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve join request and optionally replace inactive member
     *
     * @param int $groupId
     * @param int $requestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveJoinRequest($groupId, $requestId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Check if user is group admin
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Only group admin can approve join requests'], 403);
        }

        try {
            $joinRequest = DB::table('group_join_requests')
                ->where('id', $requestId)
                ->where('group_id', $groupId)
                ->first();

            if (!$joinRequest) {
                return response()->json(['message' => 'Join request not found'], 404);
            }

            if ($joinRequest->status !== 'pending') {
                return response()->json(['message' => 'Join request is no longer pending'], 400);
            }

            // Check if group is at capacity
            $activeMembers = $group->users()->where('group_user.is_active', true)->count();
            $totalUsers = $group->total_users;

            DB::transaction(function () use ($group, $joinRequest, $activeMembers, $totalUsers) {
                $replacedMemberId = null;

                // If group is at capacity, remove an inactive member
                if ($activeMembers >= $totalUsers) {
                    // Find an inactive member (invited but not accepted)
                    $inactiveMember = $group->users()
                        ->where('group_user.is_active', false)
                        ->orderBy('group_user.created_at', 'asc')
                        ->first();

                    if ($inactiveMember) {
                        $replacedMemberId = $inactiveMember->id;
                        
                        // Remove the inactive member from group
                        $group->users()->detach($inactiveMember->id);

                        // Notify the replaced member (queued, won't block)
                        try {
                            $inactiveMember->notify(new GroupMemberRemovedNotification($group->id, $group->title ?? ''));
                        } catch (\Exception $e) {
                            Log::warning('Failed to send member removed notification', ['error' => $e->getMessage()]);
                        }

                        Log::info('Inactive member replaced in group', [
                            'replaced_user_id' => $inactiveMember->id,
                            'group_id' => $group->id,
                            'new_user_id' => $joinRequest->user_id
                        ]);
                    }
                }

                // Add the new user to the group
                if (!$group->users()->where('user_id', $joinRequest->user_id)->exists()) {
                    $group->users()->attach($joinRequest->user_id, [
                        'role' => 'member',
                        'is_active' => true
                    ]);
                }

                // Update join request status
                DB::table('group_join_requests')
                    ->where('id', $joinRequest->id)
                    ->update(['status' => 'approved', 'updated_at' => now()]);

                // Notify user about approval (queued, won't block)
                $user = User::find($joinRequest->user_id);
                if ($user) {
                    try {
                        $user->notify(new GroupJoinApprovedNotification($group->id, $group->title ?? ''));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send join approved notification', ['error' => $e->getMessage()]);
                    }
                }

                Log::info('Join request approved', [
                    'user_id' => $joinRequest->user_id,
                    'group_id' => $group->id,
                    'replaced_member_id' => $replacedMemberId
                ]);
            });

            return response()->json([
                'message' => 'Join request approved successfully',
                'data' => [
                    'request_id' => $requestId,
                    'status' => 'approved',
                    'new_member_id' => $joinRequest->user_id,
                    'group_capacity_full' => $activeMembers >= $totalUsers
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to approve join request', [
                'group_id' => $groupId,
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to approve join request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject join request
     *
     * @param int $groupId
     * @param int $requestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectJoinRequest($groupId, $requestId)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Check if user is group admin
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Only group admin can reject join requests'], 403);
        }

        try {
            $joinRequest = DB::table('group_join_requests')
                ->where('id', $requestId)
                ->where('group_id', $groupId)
                ->first();

            if (!$joinRequest) {
                return response()->json(['message' => 'Join request not found'], 404);
            }

            if ($joinRequest->status !== 'pending') {
                return response()->json(['message' => 'Join request is no longer pending'], 400);
            }

            // Update join request status
            DB::table('group_join_requests')
                ->where('id', $joinRequest->id)
                ->update(['status' => 'rejected', 'updated_at' => now()]);

            // Notify user about rejection (queued, won't block)
            $user = User::find($joinRequest->user_id);
            if ($user) {
                try {
                    $user->notify(new GroupJoinRejectedNotification($group->id, $group->title ?? ''));
                } catch (\Exception $e) {
                    Log::warning('Failed to send join rejected notification', ['error' => $e->getMessage()]);
                }
            }

            Log::info('Join request rejected', [
                'user_id' => $joinRequest->user_id,
                'group_id' => $group->id,
            ]);

            return response()->json([
                'message' => 'Join request rejected successfully',
                'data' => ['request_id' => $requestId, 'status' => 'rejected']
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to reject join request', [
                'group_id' => $groupId,
                'request_id' => $requestId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to reject join request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all groups
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get all groups with their users
        $groups = Group::with('users')
            ->withCount('members')
            ->where('owner_id', Auth::id())
            ->get();

        return response()->json([
            'message' => 'Groups retrieved successfully',
            'data' => $groups
        ], 200);
    }

    /**
     * Get a specific group by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Find the group by ID
        $group = Group::with('users')
            ->withCount(['users as active_users_count' => function ($query) {
                $query->where('group_user.is_active', true);
            }])
            ->find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Load join requests with user data (only for group owner)
        $joinRequests = [];
        if ($group->owner_id === Auth::id()) {
            $joinRequests = DB::table('group_join_requests')
                ->where('group_id', $id)
                ->join('users', 'group_join_requests.user_id', '=', 'users.id')
                ->select(
                    'group_join_requests.id',
                    'group_join_requests.status',
                    'group_join_requests.created_at',
                    'group_join_requests.updated_at',
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.email as user_email'
                )
                ->orderBy('group_join_requests.created_at', 'desc')
                ->get();
        }

        return response()->json([
            'message' => 'Group retrieved successfully',
            'data' => [
                'group' => $group,
                'join_requests' => $joinRequests,
            ]
        ], 200);
    }

    /**
     * Update group details
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Find the group by ID
        $group = Group::findOrFail($id);

        // Check if the authenticated user is the owner of the group
        if ($group->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized: Only group admins can update'], 403);
        }

        // Validate request data
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'total_users' => 'sometimes|required|integer|min:2|max:300',
            'target_amount' => 'sometimes|required|numeric|min:0',
            'expected_start_date' => 'sometimes|required|date|after:today',
            'payment_out_day' => 'sometimes|required|integer|min:1|max:31',
        ]);

        // Update group details
        $group->update($validated);

        return response()->json([
            'message' => 'Group updated successfully',
            'data' => $group
        ], 200);
    }

    /**
     * Delete a group
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Find the group by ID
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Delete the group
        $group->delete();

        return response()->json([
            'message' => 'Group deleted successfully'
        ], 200);
    }
}
