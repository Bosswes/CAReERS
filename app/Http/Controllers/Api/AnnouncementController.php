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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $announcement = DB::table('announcements')->where('announcement_id', $id)->first();
        if (!$announcement) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        if (isset($announcement->registration_status) && $announcement->registration_status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Registration is closed for this event.']);
        }

        $student = DB::table('student_info')->where('student_number', session('user_id'))->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student record not found.']);
        }

        $studentNumber = $student->student_number;

        // Check in event_registrants (NOT event_attendance)
        $exists = DB::table('event_registrants')
            ->where('event_id', $id)
            ->where('student_number', $studentNumber)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You are already registered for this event.']);
        }

        $now = now()->timezone('Asia/Manila');
        $qrCode = 'EVT-' . $id . '-' . $studentNumber . '-' . time();

        // Insert to event_registrants only — attendance is recorded via QR scan
        DB::table('event_registrants')->insert([
            'event_id'       => $id,
            'student_number' => $studentNumber,
            'qr_code'        => $qrCode,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Successfully registered! Show your QR code to the admin during the event.',
            'student_number' => $studentNumber,
            'qr_code'        => $qrCode,
        ]);
    }

    public function registrationStatus(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['registered' => false]);
        }

        $student = DB::table('student_info')->where('student_number', session('user_id'))->first();
        if (!$student) {
            return response()->json(['registered' => false]);
        }

        // Check registration (event_registrants)
        $registration = DB::table('event_registrants')
            ->where('event_id', $id)
            ->where('student_number', $student->student_number)
            ->first();

        // Check attendance (event_attendance) — only set after QR scan
        $attended = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $student->student_number)
            ->exists();

        return response()->json([
            'registered'     => !is_null($registration),
            'attended'       => $attended,
            'qr_code'        => $registration->qr_code ?? null,
            'student_number' => $student->student_number,
        ]);
    }

    public function getRegistrants(Request $request, $id)
    {
        if (!session('user_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = DB::table('event_registrants as r')
            ->join('student_info as s', 'r.student_number', '=', 's.student_number')
            ->where('r.event_id', $id)
            ->select('r.*', 's.first_name', 's.last_name', 's.cvsu_email', 's.program', 's.course', 's.section');

        if ($request->filled('program')) {
            $query->where('s.course', $request->program);
        }

        if ($request->filled('section')) {
            $query->where('s.section', 'like', '%' . $request->section . '%');
        }

        $registrants = $query->get();

        $attendedNumbers = DB::table('event_attendance')
            ->where('event_id', $id)
            ->pluck('student_number')
            ->toArray();

        return response()->json([
            'success'          => true,
            'registrant_count' => $registrants->count(),
            'attendance_count' => count($attendedNumbers),
            'registrants'      => $registrants,
            'attended'         => $attendedNumbers,
        ]);
    }

    public function scanQR(Request $request, $id)
    {
        if (!session('user_logged_in') || session('user_role') !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $qrCode = $request->qr_code;

        $registrant = DB::table('event_registrants')
            ->where('event_id', $id)
            ->where('qr_code', $qrCode)
            ->first();

        if (!$registrant) {
            return response()->json(['success' => false, 'message' => 'Invalid QR code or not registered for this event.']);
        }

        $alreadyAttended = DB::table('event_attendance')
            ->where('event_id', $id)
            ->where('student_number', $registrant->student_number)
            ->exists();

        if ($alreadyAttended) {
            return response()->json(['success' => false, 'message' => 'Student already marked as attended.']);
        }

        $now = now()->timezone('Asia/Manila');

        DB::table('event_attendance')->insert([
            'event_id'        => $id,
            'student_number'  => $registrant->student_number,
            'qr_code'         => $qrCode,
            'attendance_time' => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $student = DB::table('student_info')
            ->where('student_number', $registrant->student_number)
            ->first();

        return response()->json([
            'success'        => true,
            'message'        => 'Attendance recorded successfully!',
            'student_number' => $registrant->student_number,
            'student_name'   => $student ? $student->first_name . ' ' . $student->last_name : null,
        ]);
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