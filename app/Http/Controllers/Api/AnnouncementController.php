<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('announcements')
                  ->where('is_published', true)
                  ->where(function($q) {
                      $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                  })
                  ->orderBy('start_date', 'desc');
        
        if (session('user_logged_in')) {
            $role = session('user_role');
            if ($role === 'student') {
                $query->whereIn('target_audience', ['all', 'students']);
            }
        } else {
            $query->where('target_audience', 'all');
        }
        
        $announcements = $query->get();
        
        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }
    
    public function store(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'announcement_type' => 'required',
            'start_date' => 'required|date'
        ]);
        
        $id = DB::table('announcements')->insertGetId([
            'title' => $request->title,
            'content' => $request->content,
            'announcement_type' => $request->announcement_type,
            'target_audience' => $request->target_audience ?? 'all',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'form_link' => $request->form_link,
            'registration_status' => $request->registration_status ?? 'open',
            'is_published' => $request->is_published ?? true,
            'created_by' => session('user_id'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'announcement_id' => $id,
            'message' => 'Announcement created successfully'
        ]);
    }
    
    public function update(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $updateData = [];
        if ($request->has('title')) $updateData['title'] = $request->title;
        if ($request->has('content')) $updateData['content'] = $request->content;
        if ($request->has('announcement_type')) $updateData['announcement_type'] = $request->announcement_type;
        if ($request->has('target_audience')) $updateData['target_audience'] = $request->target_audience;
        if ($request->has('start_date')) $updateData['start_date'] = $request->start_date;
        if ($request->has('end_date')) $updateData['end_date'] = $request->end_date;
        if ($request->has('location')) $updateData['location'] = $request->location;
        if ($request->has('form_link')) $updateData['form_link'] = $request->form_link;
        if ($request->has('registration_status')) $updateData['registration_status'] = $request->registration_status;
        if ($request->has('is_published')) $updateData['is_published'] = $request->is_published;
        
        $updateData['updated_at'] = now();
        
        DB::table('announcements')
            ->where('announcement_id', $id)
            ->update($updateData);
        
        return response()->json(['success' => true, 'message' => 'Announcement updated successfully']);
    }
    
    public function destroy($id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        DB::table('announcements')->where('announcement_id', $id)->delete();
        
        return response()->json(['success' => true, 'message' => 'Announcement deleted successfully']);
    }
    
    public function publish(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $announcement = DB::table('announcements')->where('announcement_id', $id)->first();
        
        DB::table('announcements')
            ->where('announcement_id', $id)
            ->update([
                'is_published' => !$announcement->is_published,
                'updated_at' => now()
            ]);
        
        return response()->json(['success' => true, 'message' => 'Announcement status updated']);
    }
}