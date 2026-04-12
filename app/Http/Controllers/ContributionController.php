<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Group;
use App\Models\User;
use App\Notifications\ContributionReceivedNotification;
use App\Notifications\ContributionRejectedNotification;
use App\Notifications\ContributionVerifiedNotification;
use App\Services\CloudinaryService;
use App\Services\NotificationService;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContributionController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Submit a contribution with optional proof of payment.
     *
     * POST /user/group/{groupId}/contribute
     */
    public function submit(Request $request, string $groupId)
    {
        $user = Auth::user();

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Must be an active member
        $membership = $group->users()
            ->where('user_id', $user->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'You are not an active member of this group'], 403);
        }

        $validated = $request->validate([
            'cycle_number' => 'required|integer|min:1',
            'due_date'     => 'required|date',
            'note'         => 'nullable|string|max:500',
            'proof'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Prevent duplicate submission for the same cycle
        $existing = Contribution::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('cycle_number', $validated['cycle_number'])
            ->first();

        if ($existing && in_array($existing->status, ['under_review', 'verified'])) {
            return response()->json([
                'message' => 'A contribution for this cycle has already been submitted.',
                'code'    => 'ALREADY_SUBMITTED',
            ], 409);
        }

        // Upload proof file to Cloudinary
        $proofPath     = null;
        $proofPublicId = null;
        if ($request->hasFile('proof')) {
            $file = $request->file('proof');

            // Validate actual MIME type via file content, not extension
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getRealPath());
            $allowed  = ['application/pdf', 'image/jpeg', 'image/png'];

            if (!in_array($mimeType, $allowed)) {
                return response()->json([
                    'message' => 'Invalid file type. Only PDF, JPG, and PNG are accepted.',
                ], 422);
            }

            $uploaded      = $this->cloudinary->upload($file, 'contributions/' . $groupId . '/' . $user->id);
            $proofPath     = $uploaded['url'];
            $proofPublicId = $uploaded['public_id'];
        }

        $dueDate = $validated['due_date'];
        $now     = now();

        try {
            $contribution = DB::transaction(function () use (
                $groupId, $user, $group, $validated, $proofPath, $proofPublicId, $dueDate, $existing
            ) {
                $data = [
                    'group_id'        => $groupId,
                    'user_id'         => $user->id,
                    'cycle_number'    => $validated['cycle_number'],
                    'amount'          => $group->payable_amount,
                    'proof_path'      => $proofPath,
                    'proof_public_id' => $proofPublicId,
                    'note'            => $validated['note'] ?? null,
                    'due_date'     => $dueDate,
                    'status'       => 'under_review',
                    'submitted_at' => now(),
                ];

                if ($existing) {
                    // Re-submission after rejection
                    $existing->update($data);
                    return $existing->fresh();
                }

                return Contribution::create($data);
            });

            // Award pay_early / pay_on_time points
            $dueDate = \Carbon\Carbon::parse($dueDate);
            if ($now->lt($dueDate)) {
                PointsService::award(
                    $user,
                    PointsService::ACTION_PAY_EARLY,
                    'Early contribution — ' . $group->title . ' cycle ' . $validated['cycle_number'],
                    ['group_id' => $group->id, 'contribution_id' => $contribution->id]
                );
            } elseif ($now->lte($dueDate->endOfDay())) {
                PointsService::award(
                    $user,
                    PointsService::ACTION_PAY_ON_TIME,
                    'On-time contribution — ' . $group->title . ' cycle ' . $validated['cycle_number'],
                    ['group_id' => $group->id, 'contribution_id' => $contribution->id]
                );
            }

            // Notify group owner that a contribution was received
            $owner = User::find($group->owner_id);
            if ($owner && $owner->id !== $user->id) {
                NotificationService::send(
                    $owner,
                    new ContributionReceivedNotification($group, $contribution, $user)
                );
            }

            return response()->json([
                'message' => 'Contribution submitted successfully',
                'data'    => $contribution,
            ], 201);
        } catch (\Exception $e) {
            // Clean up uploaded file from Cloudinary if transaction failed
            if ($proofPublicId) {
                $this->cloudinary->delete($proofPublicId);
            }

            Log::error('Contribution submission failed', [
                'user_id'  => $user->id,
                'group_id' => $groupId,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to submit contribution',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List the authenticated user's contributions in a group.
     *
     * GET /user/group/{groupId}/contributions
     */
    public function index(Request $request, string $groupId)
    {
        $user = Auth::user();

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        $isMember = $group->users()
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'You are not a member of this group'], 403);
        }

        $contributions = Contribution::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->orderByDesc('cycle_number')
            ->paginate(20);

        return response()->json([
            'message' => 'Contributions retrieved successfully',
            'data'    => $contributions,
        ]);
    }

    /**
     * Verify a contribution (group admin/owner only).
     *
     * PUT /user/group/{groupId}/contributions/{id}/verify
     */
    public function verify(string $groupId, string $id)
    {
        $user = Auth::user();

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        if ($group->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: Only the group admin can verify contributions'], 403);
        }

        $contribution = Contribution::with(['group', 'user'])
            ->where('group_id', $groupId)
            ->find($id);

        if (!$contribution) {
            return response()->json(['message' => 'Contribution not found'], 404);
        }

        if ($contribution->status === 'verified') {
            return response()->json(['message' => 'Contribution is already verified'], 409);
        }

        try {
            DB::transaction(function () use ($contribution, $group) {
                $contribution->update(['status' => 'verified']);

                // Check if all active members have verified contributions for this cycle
                $activeMemberCount = $group->users()
                    ->wherePivot('is_active', true)
                    ->count();

                $verifiedCount = Contribution::where('group_id', $group->id)
                    ->where('cycle_number', $contribution->cycle_number)
                    ->where('status', 'verified')
                    ->count();

                if ($verifiedCount >= $activeMemberCount) {
                    // Award cycle completed points to all active members
                    $group->users()
                        ->wherePivot('is_active', true)
                        ->get()
                        ->each(function (User $u) use ($group, $contribution) {
                            PointsService::award(
                                $u,
                                PointsService::ACTION_CYCLE_COMPLETED,
                                'Cycle ' . $contribution->cycle_number . ' completed — ' . $group->title,
                                ['group_id' => $group->id, 'cycle_number' => $contribution->cycle_number]
                            );
                        });
                }
            });

            // Notify contributor their payment was verified
            NotificationService::send(
                $contribution->user,
                new ContributionVerifiedNotification($contribution->group, $contribution)
            );

            return response()->json([
                'message' => 'Contribution verified successfully',
                'data'    => $contribution->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Contribution verification failed', [
                'contribution_id' => $id,
                'error'           => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to verify contribution',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a contribution (group admin/owner only).
     *
     * PUT /user/group/{groupId}/contributions/{id}/reject
     */
    public function reject(string $groupId, string $id)
    {
        $user = Auth::user();

        $group = Group::find($groupId);
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        if ($group->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized: Only the group admin can reject contributions'], 403);
        }

        $contribution = Contribution::with(['group', 'user'])
            ->where('group_id', $groupId)
            ->find($id);

        if (!$contribution) {
            return response()->json(['message' => 'Contribution not found'], 404);
        }

        if ($contribution->status === 'verified') {
            return response()->json(['message' => 'Cannot reject a verified contribution'], 409);
        }

        try {
            $contribution->update(['status' => 'rejected']);

            NotificationService::send(
                $contribution->user,
                new ContributionRejectedNotification($contribution->group, $contribution)
            );

            return response()->json([
                'message' => 'Contribution rejected',
                'data'    => $contribution->fresh(),
            ]);
        } catch (\Exception $e) {
            Log::error('Contribution rejection failed', [
                'contribution_id' => $id,
                'error'           => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to reject contribution',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stream a private proof file to the authenticated user.
     *
     * GET /user/group/{groupId}/contributions/{id}/proof
     */
    public function proof(string $groupId, string $id)
    {
        $user = Auth::user();

        $contribution = Contribution::find($id);
        if (!$contribution || $contribution->group_id !== $groupId) {
            return response()->json(['message' => 'Contribution not found'], 404);
        }

        // Only the contributor or a group admin may download proof
        $isContributor = $contribution->user_id === $user->id;
        $isAdmin       = $contribution->group->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();

        if (!$isContributor && !$isAdmin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!$contribution->proof_path) {
            return response()->json(['message' => 'No proof file found'], 404);
        }

        return redirect($contribution->proof_path);
    }
}
