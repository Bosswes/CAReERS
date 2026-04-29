<!DOCTYPE html>
<html>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;overflow:hidden;">
    <div style="background:#2E7D32;padding:24px;text-align:center;">
        <h2 style="color:white;margin:0;">📢 New Announcement</h2>
    </div>
    <div style="padding:24px;">
        <p>Hi <strong>{{ $studentName }}</strong>,</p>
        <p>There is a new announcement for you:</p>
        <div style="background:#f0fdf4;border-left:4px solid #2E7D32;padding:16px;border-radius:8px;margin:16px 0;">
            <h3 style="margin:0 0 8px;color:#2E7D32;">{{ $announcementTitle }}</h3>
            <p style="margin:4px 0;">{{ $content }}</p>
            <p style="margin:8px 0 0;font-size:12px;color:#64748b;">📅 {{ $startDate }}</p>
        </div>
        <p>Login to your CAReERS account to see more details.</p>
    </div>
    <div style="background:#f8fafc;padding:16px;text-align:center;font-size:12px;color:#94a3b8;">
        CAReERS - CvSU Carmona Job Recommendation System
    </div>
</div>
</body>
</html>