<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // Kunin ang notifications ng student
    public function index(Request $request)
    {
        $studentId = session('user_id');

        $notifications = DB::table('student_notifications')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'success'      => true,
            'notifications'=> $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    // Mark as read
    public function markRead($id)
    {
        DB::table('student_notifications')
            ->where('id', $id)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // Mark lahat as read
    public function markAllRead(Request $request)
    {
        $studentId = session('user_id');

        DB::table('student_notifications')
            ->where('student_id', $studentId)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}