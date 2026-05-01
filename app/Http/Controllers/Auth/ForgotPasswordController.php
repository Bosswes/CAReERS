<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $student = DB::table('student_info')->where('cvsu_email', $email)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'No account found with that email.'], 404);
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $token = Str::random(64);
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => hash('sha256', $token),
            'created_at' => Carbon::now(),
        ]);

        $resetUrl = config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($email);

        Mail::html('
            <div style="font-family:sans-serif;max-width:600px;margin:auto;padding:32px;">
                <h2 style="color:#2d6a2d;">CAReERS — Password Reset</h2>
                <p>Hello, <strong>' . htmlspecialchars($student->first_name) . '</strong>!</p>
                <p>Click the button below to reset your password. This link expires in <strong>60 minutes</strong>.</p>
                <a href="' . $resetUrl . '" style="display:inline-block;background:#2d6a2d;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;margin:16px 0;">Reset Password</a>
                <p style="color:#888;font-size:13px;">If you did not request this, ignore this email.</p>
            </div>',
            function ($message) use ($email) {
                $message->to($email)->subject('CAReERS — Reset Your Password');
            }
        );

        return response()->json(['success' => true, 'message' => 'Password reset link sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || $record->token !== hash('sha256', $request->token)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired reset token.'], 422);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['success' => false, 'message' => 'Link expired. Please request a new one.'], 422);
        }

        DB::table('student_info')
            ->where('cvsu_email', $request->email)
            ->update(['password' => Hash::make($request->password), 'updated_at' => Carbon::now()]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password reset successful! You can now login.']);
    }
}