<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadReceivedNotification;
use App\Notifications\NewLeadAdminNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create($request->validatedSnake());

        // Confirmation to the lead
        NotificationService::send($lead, new LeadReceivedNotification($lead));

        // Alert all admin users (mail + database)
        User::role('admin')->get()->each(
            fn ($admin) => NotificationService::send($admin, new NewLeadAdminNotification($lead))
        );

        return response()->json([
            'message' => 'Thank you! Your response has been recorded.',
            'data'    => $lead,
        ], 201);
    }

    /**
     * List all leads (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);

        $leads = Lead::orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'message' => 'Leads retrieved successfully.',
            'data'     => $leads->items(),
            'pagination' => [
                'total'        => $leads->total(),
                'per_page'     => $leads->perPage(),
                'current_page' => $leads->currentPage(),
                'last_page'    => $leads->lastPage(),
                'from'         => $leads->firstItem(),
                'to'           => $leads->lastItem(),
            ],
        ], 200);
    }

    /**
     * Show a single lead (admin only).
     */
    public function show(string $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        return response()->json([
            'message' => 'Lead retrieved successfully.',
            'data'    => $lead,
        ], 200);
    }

    /**
     * Delete a lead (admin only).
     */
    public function destroy(string $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully.'], 200);
    }
}
