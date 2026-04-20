<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // API: Get attendance list for an event
    public function index($eventId)
    {
        $attendance = DB::table('event_attendance')
            ->join('student_info', 'event_attendance.student_number', '=', 'student_info.student_number')
            ->where('event_attendance.event_id', $eventId)
            ->select(
                'student_info.student_number',
                'student_info.first_name',
                'student_info.last_name',
                'student_info.middle_name',
                'student_info.course',
                'student_info.year_level',
                'student_info.section',
                'event_attendance.attendance_time'
            )
            ->orderBy('student_info.last_name')
            ->get();

        return response()->json(['success' => true, 'attendance' => $attendance]);
    }

    // API: Log attendance via QR scan
    public function log(Request $request)
    {
        $request->validate([
            'event_id' => 'required',
            'student_number' => 'required'
        ]);

        // Get Philippines time (UTC+8)
        $now = now('Asia/Manila');
        $todayPH = $now->toDateString(); // e.g. 2026-03-31
        $nowUtc = $now->utc(); // i-store as UTC sa DB para consistent

        // Check if today matches the event date
        $event = DB::table('announcements')->where('announcement_id', $request->event_id)->first();

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found']);
        }

        // Allow scanning only on the event date (or within start_date to end_date range)
        $eventStart = $event->start_date;
        $eventEnd   = $event->end_date ?? $event->start_date;

        if ($todayPH < $eventStart) {
            return response()->json([
                'success' => false,
                'message' => 'Event has not started yet. Scanning opens on ' . date('F d, Y', strtotime($eventStart)) . '.'
            ]);
        }

        if ($todayPH > $eventEnd) {
            return response()->json([
                'success' => false,
                'message' => 'Event has already ended. Attendance scanning is closed.'
            ]);
        }

        // Check if already logged
        $exists = DB::table('event_attendance')
            ->where('event_id', $request->event_id)
            ->where('student_number', $request->student_number)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Attendance already recorded for this student.']);
        }

        // Check student exists
        $student = DB::table('student_info')
            ->where('student_number', $request->student_number)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        DB::table('event_attendance')->insert([
            'event_id'        => $request->event_id,
            'student_number'  => $request->student_number,
            'attendance_time' => $nowUtc,
            'created_at'      => $nowUtc,
            'updated_at'      => $nowUtc,
        ]);

        return response()->json(['success' => true, 'message' => 'Attendance logged', 'student' => $student]);
    }

    // Web: QR Scanner page
    public function scan($id)
    {
        $event = DB::table('announcements')->where('announcement_id', $id)->first();
        if (!$event) abort(404);
        return view('attendance.scan', compact('event'));
    }

    // Web: Printable attendance page
    public function printView($id)
    {
        $event = DB::table('announcements')->where('announcement_id', $id)->first();
        if (!$event) abort(404);

        $attendance = DB::table('event_attendance')
            ->join('student_info', 'event_attendance.student_number', '=', 'student_info.student_number')
            ->where('event_attendance.event_id', $id)
            ->select(
                'student_info.student_number',
                'student_info.first_name',
                'student_info.last_name',
                'student_info.middle_name',
                'student_info.course',
                'student_info.year_level',
                'student_info.section',
                'event_attendance.attendance_time'
            )
            ->orderBy('student_info.last_name')
            ->get();

        // Format attendance times to Philippines time
        $attendance = $attendance->map(function($record) {
            $record->attendance_time = \Carbon\Carbon::parse($record->attendance_time)
                ->timezone('Asia/Manila')
                ->format('h:i:s A');
            return $record;
        });

        return view('attendance.print', compact('event', 'attendance'));
    }
}
