<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $username = $request->username;
            $password = $request->password;

            // Check Student
            $student = DB::table('student_info')
                        ->where('cvsu_email', $username)
                        ->orWhere('student_number', $username)
                        ->first();

            if ($student && Hash::check($password, $student->password)) {
                session([
                    'user_logged_in' => true,
                    'user_id' => $student->student_number,
                    'user_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'user_email' => $student->cvsu_email ?? '',
                    'user_role' => 'student',
                ]);
                
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $student->student_number,
                        'name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                        'email' => $student->cvsu_email ?? '',
                        'student_number' => $student->student_number,
                        'role' => 'student',
                    ]
                ]);
            }

            // Check Admin (Coordinator)
            $admin = DB::table('admin')->where('username', $username)->orWhere('admin_email', $username)->first();
            
            if ($admin && Hash::check($password, $admin->password)) {
                session([
                    'user_logged_in' => true,
                    'user_id' => $admin->admin_id,
                    'user_name' => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                    'user_email' => $admin->admin_email ?? $admin->username,
                    'user_role' => 'admin',
                ]);
                
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $admin->admin_id,
                        'name' => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                        'email' => $admin->admin_email ?? $admin->username,
                        'username' => $admin->username,
                        'role' => 'admin',
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Login error: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        session()->flush();
        return response()->json(['success' => true]);
    }

    public function user(Request $request)
    {
        if (!session('user_logged_in')) {
            return response()->json(['user' => null]);
        }
        
        return response()->json([
            'user' => [
                'id' => session('user_id'),
                'name' => session('user_name'),
                'email' => session('user_email'),
                'role' => session('user_role'),
            ]
        ]);
    }
}