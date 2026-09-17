<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get unread notification count.
     */
    public function unread_count(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'count' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }

    /**
     * Get recent notifications list for navbar dropdown.
     */
    public function recent(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $notifications = $user->notifications()->latest()->limit(10)->get()->map(function ($n) {
            return [
                'id'         => $n->id,
                'read'       => !is_null($n->read_at),
                'data'       => $n->data,
                'created_at' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function mark_read(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'unread_count' => Auth::user()->unreadNotifications()->count()]);
        }

        return response()->json(['status' => 'failed', 'message' => 'Notifikasi tidak ditemukan.'], 404);
    }

    /**
     * Mark all user notifications as read.
     */
    public function mark_all_read(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'status'       => 'success',
            'unread_count' => 0,
        ]);
    }
}
