<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GroupController extends Controller
{

    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Validate request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'total_users' => 'required|integer|min:2|max:300',
            'target_amount' => 'required|numeric|min:0',
            'expected_start_date' => 'required|date|after:today',
            'payment_out_day' => 'required|integer|min:1|max:31',
            'membersEmails' => 'array',
            'membersEmails.*' => 'required|email'
        ]);

        // Calculate payable amount and expected end date
        $payableAmount = (float) $validated['target_amount'] / (int) $validated['total_users'];
        $expectedEndDate = \Carbon\Carbon::parse($validated['expected_start_date'])
            ->addMonths($validated['total_users'])
            ->format('Y-m-d');

        try {
            DB::beginTransaction();

            // Create the group
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

            // Attach the creator as the first member
            $group->users()->attach(Auth::id(), [
                'role' => 'admin',
                'is_active' => true
            ]);

                // Ensure the User model is imported: use App\Models\User;
                foreach ($validated['membersEmails'] as $email) {
                    // Skip if the email belongs to the creator
                    if ($email === Auth::user()->email) {
                        continue;
                    }

                    // Register the user if not exists
                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => explode('@', $email)[0], // Use the part before '@' as name
                            'password' => Hash::make(random_bytes(10))
                        ]
                    );

                    // Attach user to the group with default member role (inactive until confirmation, if needed)
                    $group->users()->attach($user->id, [
                        'role' => 'member',
                        'is_active' => false
                    ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Group created successfully',
                'data' => $group
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Group creation failed',
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
        $group = Group::with('users', 'members')->find($id);

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
