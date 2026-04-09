<!DOCTYPE html>
<html>
<head>
    <title>Attendance - {{ $event->title }}</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h2 { text-align: center; }
        p { text-align: center; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #1a4731; color: white; padding: 8px; text-align: left; }
        td { border: 1px solid #ccc; padding: 8px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn { background: #16a34a; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; margin: 10px 5px; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .sig-line { text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 5px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; margin-bottom: 20px;">
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <a href="/attendance/scan/{{ $event->announcement_id }}" class="btn" style="text-decoration:none;">📷 Back to Scanner</a>
    </div>

    <h2>ATTENDANCE SHEET</h2>
    <p><strong>Event:</strong> {{ $event->title }}</p>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}
       @if($event->location) &nbsp;|&nbsp; <strong>Location:</strong> {{ $event->location }} @endif</p>
    <p><strong>Total Attendees:</strong> {{ count($attendance) }}</p>
    <p><strong>Printed:</strong> {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F d, Y h:i:s A') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student No.</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Course</th>
                <th>Year</th>
                <th>Section</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendance as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $a->student_number }}</td>
                <td>{{ $a->last_name }}</td>
                <td>{{ $a->first_name }}</td>
                <td>{{ $a->middle_name }}</td>
                <td>{{ $a->course }}</td>
                <td>{{ $a->year_level }}</td>
                <td>{{ $a->section }}</td>
                <td>{{ \Carbon\Carbon::parse($a->attendance_time)->timezone('Asia/Manila')->format('h:i:s A') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;">No attendance recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-line">Prepared by</div>
        <div class="sig-line">Checked by</div>
        <div class="sig-line">Noted by</div>
    </div>
</body>
</html>