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
    
    public function registerStudent(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $studentNumber = session('student_number'); // adjust kung iba ang session key mo

        $announcement = DB::table('announcements')->where('announcement_id', $id)->first();
        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        // Check if registration is open
        if (isset($announcement->registration_status) && $announcement->registration_status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Registration is closed for this event.']);
        }

        // Check if already registered
        $exists = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $studentNumber)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You are already registered for this event.']);
        }

        $now = now()->timezone('Asia/Manila');

        DB::table('event_attendance')->insert([
            'event_id'        => $id,
            'student_number'  => $studentNumber,
            'attendance_time' => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        return response()->json(['success' => true, 'message' => 'Successfully registered for this event!']);
    }

    public function registrationStatus(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['registered' => false]);
        }

        $studentNumber = session('student_number');

        $registered = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $studentNumber)
            ->exists();

        return response()->json(['registered' => $registered]);
    }

    public function registerStudent(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $announcement = DB::table('announcements')->where('announcement_id', $id)->first();
        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        if (isset($announcement->registration_status) && $announcement->registration_status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Registration is closed for this event.']);
        }

        // Get student_number from student_info using session user_id
        $student = DB::table('student_info')->where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student record not found.']);
        }

        $studentNumber = $student->student_number;

        $exists = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $studentNumber)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You are already registered for this event.']);
        }

        $now = now()->timezone('Asia/Manila');

        DB::table('event_attendance')->insert([
            'event_id'        => $id,
            'student_number'  => $studentNumber,
            'attendance_time' => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        return response()->json(['success' => true, 'message' => 'Successfully registered!', 'student_number' => $studentNumber]);
    }

    public function registrationStatus(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['registered' => false]);
        }

        $student = DB::table('student_info')->where('user_id', session('user_id'))->first();
        if (!$student) {
            return response()->json(['registered' => false]);
        }

        $registered = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $student->student_number)
            ->exists();

        return response()->json(['registered' => $registered, 'student_number' => $student->student_number]);
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