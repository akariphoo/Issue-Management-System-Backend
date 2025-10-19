<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getAllForUser($userId) {
        return Notification::where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function markAsRead($id) {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Marked as read']);
    }
}
