<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\GroupInvitation;
use App\Events\GroupCreated;

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
            'members_emails' => 'required|array', // Changed from members_emails
            'members_emails.*' => 'required|email' // Changed from members_emails.*
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
                DB::transaction(function() use ($group, $email) {
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
            Mail::to($user->email)->send(new GroupInvitation($group, $user, $generatedPassword));
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

        $group->users()->updateExistingPivot($user->id, ['is_active' => true]);

        return response()->json([
            'message' => 'Invitation accepted successfully',
            'data' => $group
        ], 200);
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
            ->where('owner_id', Auth::id())->get();

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
        $group = Group::with('users')->find($id);

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json([
            'message' => 'Group retrieved successfully',
            'data' => $group
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
