<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\NotificationService;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketUpdatedNotification;
use App\Notifications\TicketReplyNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class SupportTicketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/support/tickets",
     *     tags={"Support"},
     *     summary="Get user's support tickets",
     *     description="Returns all support tickets for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (open, in_review, awaiting, escalated, resolved, closed)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority (low, medium, high, critical)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tickets retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $query = SupportTicket::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by priority
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            // Filter active only
            if ($request->boolean('active_only')) {
                $query->open();
            }

            $tickets = $query->get()->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'category_label' => SupportTicket::getCategoryLabel($ticket->category),
                    'priority' => $ticket->priority,
                    'priority_info' => SupportTicket::getPriorityInfo($ticket->priority),
                    'status' => $ticket->status,
                    'status_label' => SupportTicket::getStatusLabel($ticket->status),
                    'is_active' => $ticket->isActive(),
                    'sla_deadline' => $ticket->sla_deadline,
                    'sla_breached' => $ticket->isSlaBreached(),
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'date' => $ticket->created_at->format('Y-m-d'),
                    'updated' => $ticket->updated_at->format('Y-m-d'),
                ];
            });

            // Calculate stats
            $stats = [
                'total' => $tickets->count(),
                'open' => $tickets->filter(fn($t) => in_array($t['status'], ['open', 'in_review', 'awaiting', 'escalated']))->count(),
                'resolved' => $tickets->where('status', 'resolved')->count(),
                'closed' => $tickets->where('status', 'closed')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'stats' => $stats,
                'tickets' => $tickets,
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to fetch tickets: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to fetch tickets',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/support/tickets",
     *     tags={"Support"},
     *     summary="Create a new support ticket",
     *     description="Submit a new support request",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"subject","category","message"},
     *             @OA\Property(property="subject", type="string", example="Payment not showing"),
     *             @OA\Property(property="category", type="string", example="payments"),
     *             @OA\Property(property="priority", type="string", example="medium"),
     *             @OA\Property(property="message", type="string", example="My payment from yesterday is not showing in my dashboard")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'category' => ['required', Rule::in([
                    'account', 'groups', 'payments', 'payouts',
                    'notifications', 'billing', 'technical', 'fraud'
                ])],
                'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'critical'])],
                'message' => 'required|string|min:20|max:5000',
            ], [
                'subject.required' => 'Subject is required',
                'category.required' => 'Please select a category',
                'category.in' => 'Invalid category selected',
                'message.required' => 'Please describe your issue',
                'message.min' => 'Please add more detail (min 20 characters)',
            ]);

            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'category' => $validated['category'],
                'priority' => $validated['priority'] ?? 'medium',
                'message' => $validated['message'],
                'status' => SupportTicket::STATUS_OPEN,
            ]);

            // Send confirmation notification
            NotificationService::send($user, new TicketCreatedNotification($ticket));

            Log::info('Support ticket created', [
                'ticket_id' => $ticket->ticket_id,
                'user_id' => $user->id,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket submitted successfully',
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'sla' => SupportTicket::getPriorityInfo($ticket->priority)['sla'],
                    'created_at' => $ticket->created_at,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Failed to create ticket: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to submit ticket',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/support/tickets/{ticketId}",
     *     tags={"Support"},
     *     summary="Get ticket details",
     *     description="Get detailed information about a specific ticket",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticketId",
     *         in="path",
     *         required=true,
     *         description="Ticket ID (e.g., TK-001)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket details retrieved"
     *     )
     * )
     */
    public function show(string $ticketId): JsonResponse
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('ticket_id', $ticketId)
                ->where('user_id', $user->id)
                ->with(['replies' => function ($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Ticket not found',
                ], 404);
            }

            // Build timeline
            $timeline = $this->buildTimeline($ticket);

            return response()->json([
                'status' => 'success',
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'category_label' => SupportTicket::getCategoryLabel($ticket->category),
                    'priority' => $ticket->priority,
                    'priority_info' => SupportTicket::getPriorityInfo($ticket->priority),
                    'status' => $ticket->status,
                    'status_label' => SupportTicket::getStatusLabel($ticket->status),
                    'message' => $ticket->message,
                    'is_active' => $ticket->isActive(),
                    'sla_deadline' => $ticket->sla_deadline,
                    'sla_breached' => $ticket->isSlaBreached(),
                    'assigned_to' => $ticket->assigned_to,
                    'first_response_at' => $ticket->first_response_at,
                    'resolved_at' => $ticket->resolved_at,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'date' => $ticket->created_at->format('Y-m-d'),
                    'updated' => $ticket->updated_at->format('Y-m-d'),
                ],
                'replies' => $ticket->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'is_from_support' => $reply->is_from_support,
                        'author' => $reply->author_name,
                        'attachments' => $reply->attachments,
                        'created_at' => $reply->created_at,
                    ];
                }),
                'timeline' => $timeline,
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to fetch ticket: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to fetch ticket details',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/support/tickets/{ticketId}/reply",
     *     tags={"Support"},
     *     summary="Reply to a ticket",
     *     description="Add a reply to an existing ticket",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticketId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reply added successfully"
     *     )
     * )
     */
    public function reply(Request $request, string $ticketId): JsonResponse
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('ticket_id', $ticketId)
                ->where('user_id', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Ticket not found',
                ], 404);
            }

            if (!$ticket->isActive()) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Cannot reply to a closed ticket',
                ], 400);
            }

            $validated = $request->validate([
                'message' => 'required|string|min:5|max:5000',
            ]);

            $reply = SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'is_from_support' => false,
                'message' => $validated['message'],
            ]);

            // Update ticket status if it was awaiting user response
            if ($ticket->status === SupportTicket::STATUS_AWAITING) {
                $ticket->update(['status' => SupportTicket::STATUS_IN_REVIEW]);
            }

            $ticket->touch(); // Update the updated_at timestamp

            Log::info('Ticket reply added', [
                'ticket_id' => $ticket->ticket_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Reply sent successfully',
                'reply' => [
                    'id' => $reply->id,
                    'message' => $reply->message,
                    'is_from_support' => $reply->is_from_support,
                    'author' => $reply->author_name,
                    'created_at' => $reply->created_at,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Failed to add reply: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to send reply',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/support/tickets/{ticketId}/escalate",
     *     tags={"Support"},
     *     summary="Escalate a ticket",
     *     description="Escalate a ticket to senior support",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticketId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket escalated successfully"
     *     )
     * )
     */
    public function escalate(string $ticketId): JsonResponse
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('ticket_id', $ticketId)
                ->where('user_id', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Ticket not found',
                ], 404);
            }

            if (!$ticket->isActive()) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Cannot escalate a resolved ticket',
                ], 400);
            }

            if ($ticket->status === SupportTicket::STATUS_ESCALATED) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Ticket is already escalated',
                ], 400);
            }

            $ticket->update([
                'status' => SupportTicket::STATUS_ESCALATED,
                'priority' => SupportTicket::PRIORITY_HIGH, // Auto-upgrade priority
            ]);

            // Add system reply
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'is_from_support' => true,
                'agent_name' => 'System',
                'message' => 'This ticket has been escalated to senior support and will be reviewed with higher priority.',
            ]);

            Log::info('Ticket escalated', [
                'ticket_id' => $ticket->ticket_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket escalated to senior support',
                'ticket' => [
                    'ticket_id' => $ticket->ticket_id,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                ],
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to escalate ticket: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to escalate ticket',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/support/tickets/{ticketId}/feedback",
     *     tags={"Support"},
     *     summary="Submit feedback on resolution",
     *     description="Mark resolution as helpful or not helpful",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="ticketId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"helpful"},
     *             @OA\Property(property="helpful", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feedback submitted"
     *     )
     * )
     */
    public function feedback(Request $request, string $ticketId): JsonResponse
    {
        try {
            $user = Auth::user();

            $ticket = SupportTicket::where('ticket_id', $ticketId)
                ->where('user_id', $user->id)
                ->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Ticket not found',
                ], 404);
            }

            if ($ticket->status !== SupportTicket::STATUS_RESOLVED) {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Can only provide feedback on resolved tickets',
                ], 400);
            }

            $validated = $request->validate([
                'helpful' => 'required|boolean',
                'comment' => 'sometimes|string|max:500',
            ]);

            // Record feedback as a reply
            $message = $validated['helpful']
                ? 'User marked this resolution as helpful.'
                : 'User indicated resolution needs more work.';

            if (!empty($validated['comment'])) {
                $message .= ' Comment: ' . $validated['comment'];
            }

            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'is_from_support' => false,
                'message' => $message,
            ]);

            // If not helpful, reopen the ticket
            if (!$validated['helpful']) {
                $ticket->update([
                    'status' => SupportTicket::STATUS_IN_REVIEW,
                    'resolved_at' => null,
                ]);
            } else {
                // Close the ticket
                $ticket->update([
                    'status' => SupportTicket::STATUS_CLOSED,
                    'closed_at' => now(),
                ]);
            }

            Log::info('Ticket feedback submitted', [
                'ticket_id' => $ticket->ticket_id,
                'helpful' => $validated['helpful'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $validated['helpful']
                    ? 'Thanks for your feedback! The ticket has been closed.'
                    : 'We\'ll look into it further.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to submit feedback: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to submit feedback',
            ], 500);
        }
    }

    /**
     * Build progress timeline for a ticket
     */
    private function buildTimeline(SupportTicket $ticket): array
    {
        return [
            [
                'label' => 'Ticket submitted',
                'time' => $ticket->created_at->format('Y-m-d'),
                'icon' => '📩',
                'done' => true,
            ],
            [
                'label' => 'Under review by support',
                'time' => $ticket->first_response_at?->format('Y-m-d'),
                'icon' => '🔍',
                'done' => $ticket->status !== SupportTicket::STATUS_OPEN,
            ],
            [
                'label' => 'Response sent to you',
                'time' => $ticket->first_response_at?->format('Y-m-d'),
                'icon' => '💬',
                'done' => in_array($ticket->status, [
                    SupportTicket::STATUS_AWAITING,
                    SupportTicket::STATUS_ESCALATED,
                    SupportTicket::STATUS_RESOLVED,
                    SupportTicket::STATUS_CLOSED,
                ]),
            ],
            [
                'label' => 'Resolved & closed',
                'time' => $ticket->resolved_at?->format('Y-m-d'),
                'icon' => '✅',
                'done' => in_array($ticket->status, [
                    SupportTicket::STATUS_RESOLVED,
                    SupportTicket::STATUS_CLOSED,
                ]),
            ],
        ];
    }
}
