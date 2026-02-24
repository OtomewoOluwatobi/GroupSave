<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class NotificationController extends Controller
{
    /**
     * Get all user notifications (paginated)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications();
        
        // Filter by read/unread status
        if ($request->has('read')) {
            $read = filter_var($request->read, FILTER_VALIDATE_BOOLEAN);
            if ($read) {
                $query->whereNotNull('read_at');
            } else {
                $query->whereNull('read_at');
            }
        }
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }
        
        $notifications = $query->paginate(20);
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get unread notifications only
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 15);

        $notifications = $user->unreadNotifications()
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Unread notifications retrieved successfully',
            'data' => $notifications->items(),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ],
        ]);
    }

    /**
     * Get count of unread notifications
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $user->unreadNotificationsCount();

        return response()->json([
            'status' => 'success',
            'data' => [
                'unread_count' => $count,
            ],
        ]);
    }

    /**
     * Get a specific notification
     * 
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     */
    public function show(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $notification,
        ]);
    }

    /**
     * Mark a notification as read
     * 
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     */
    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark a notification as unread
     * 
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     */
    public function markAsUnread(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsUnread();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as unread',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $count = $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => "Marked {$count} notifications as read",
            'data' => [
                'marked_count' => $count,
            ],
        ]);
    }

    /**
     * Delete a notification
     * 
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     */
    public function destroy(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Delete all notifications for user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAll(Request $request): JsonResponse
    {
        $user = $request->user();

        $count = $user->notifications()->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Deleted {$count} notifications",
            'data' => [
                'deleted_count' => $count,
            ],
        ]);
    }

    /**
     * Get notifications by type
     * 
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 15);

        $notifications = Notification::where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->ofType($type)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => "Notifications of type '{$type}' retrieved successfully",
            'data' => $notifications->items(),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ],
        ]);
    }
}
