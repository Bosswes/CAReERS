<!DOCTYPE html>
<html>
<head>
    <title>Scan QR - {{ $event->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; text-align: center; padding: 20px; background: #0a1628; color: white; }
        h2 { color: #4ade80; }
        #reader { width: 300px; margin: 20px auto; }
        #status { margin: 10px; padding: 10px; border-radius: 8px; }
        .success { background: #16a34a; }
        .error { background: #dc2626; }
        .btn { background: #16a34a; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; margin: 10px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <h2>📷 QR Scanner</h2>
    <p>{{ $event->title }}</p>
    <p style="color:#94a3b8; font-size:13px;">
        📅 Event Date: <strong style="color:#4ade80;">
            {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}
            @if($event->end_date && $event->end_date !== $event->start_date)
                – {{ \Carbon\Carbon::parse($event->end_date)->format('F d, Y') }}
            @endif
        </strong>
    </p>

    @php
        $todayPH    = \Carbon\Carbon::now()->timezone('Asia/Manila')->toDateString();
        $eventStart = $event->start_date;
        $eventEnd   = $event->end_date ?? $event->start_date;
        $scanAllowed = ($todayPH >= $eventStart && $todayPH <= $eventEnd);
    @endphp

    @if(!$scanAllowed)
        <div style="background:#7f1d1d; color:#fecaca; padding:16px; border-radius:10px; margin:20px auto; max-width:320px; font-size:14px;">
            @if($todayPH < $eventStart)
                🔒 Scanning not yet open.<br>
                <strong>Opens on {{ \Carbon\Carbon::parse($eventStart)->format('F d, Y') }}</strong>
            @else
                🔒 Event has ended.<br>
                <strong>Scanning closed since {{ \Carbon\Carbon::parse($eventEnd)->format('F d, Y') }}</strong>
            @endif
        </div>
    @endif

    <div id="reader" style="{{ !$scanAllowed ? 'display:none;' : '' }}"></div>
    <div id="status"></div>
    <a href="/attendance/print/{{ $event->announcement_id }}" class="btn">🖨️ View Attendance Sheet</a>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const statusDiv = document.getElementById('status');
        let scanning = true;

        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            async (decodedText) => {
                if (!scanning) return;
                scanning = false;

                try {
                    const res = await fetch('/attendance/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            event_id: {{ $event->announcement_id }},
                            student_number: decodedText
                        })
                    });

                    const data = await res.json();
                    statusDiv.className = data.success ? 'success' : 'error';
                    statusDiv.innerText = data.success
                        ? `✅ ${data.student.first_name} ${data.student.last_name} - Logged!`
                        : `❌ ${data.message}`;

                    // Resume scanning after 2 seconds
                    setTimeout(() => { scanning = true; statusDiv.innerText = ''; }, 2000);
                } catch (e) {
                    statusDiv.className = 'error';
                    statusDiv.innerText = '❌ Error logging attendance';
                    setTimeout(() => { scanning = true; }, 2000);
                }
            }
       );
    </script>
</body>
</html>