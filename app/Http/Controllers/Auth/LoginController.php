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
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $username = $request->login;
            $password = $request->password;

            // Check Student
            $student = DB::table('student_info')
                        ->where(function($query) use ($username) {
                            $query->where('cvsu_email', $username)
                                  ->orWhere('student_number', $username);
                        })
                        ->first();

            if ($student && Hash::check($password, $student->password)) {
                session([
                    'user_logged_in' => true,
                    'user_id'        => $student->student_number,
                    'user_name'      => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                    'user_email'     => $student->cvsu_email ?? '',
                    'user_role'      => 'student',
                ]);

                return response()->json([
                    'success' => true,
                    'user' => [
                        'id'             => $student->student_number,
                        'name'           => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                        'email'          => $student->cvsu_email ?? '',
                        'student_number' => $student->student_number,
                        'student_id'     => $student->student_number,
                        'role'           => 'student',
                    ],
                    'registrationData' => [
                        'shsSchool'           => $student->shs_school ?? '',
                        'shsYearGrad'         => $student->shs_year_grad ?? '',
                        'shsType'             => $student->shs_type ?? '',
                        'hsSchool'            => $student->hs_school ?? '',
                        'hsYearGrad'          => $student->hs_year_grad ?? '',
                        'hsType'              => $student->hs_type ?? '',
                        'elemSchool'          => $student->elem_school ?? '',
                        'elemYearGrad'        => $student->elem_year_grad ?? '',
                        'elemType'            => $student->elem_type ?? '',
                        'parentGuardian'      => $student->parent_guardian ?? '',
                        'parentRelationship'  => $student->parent_relation ?? '',
                        'parentCellphone'     => $student->parent_cellphone ?? '',
                        'parentAddress'       => $student->parent_address ?? '',
                        'parentOccupation'    => $student->parent_occupation ?? '',
                        // Personal details — para laging available sa resume kahit mag-relogin
                        'dateOfBirth'         => $student->birth_date ?? '',
                        'birthPlace'          => $student->birth_place ?? '',
                        'fullAddress'         => $student->full_address ?? '',
                    ]
                ]);
            }

            // Check Admin
            $admin = DB::table('admin')
                        ->where(function($query) use ($username) {
                            $query->where('username', $username)
                                  ->orWhere('admin_email', $username);
                        })
                        ->first();

            if ($admin && Hash::check($password, $admin->password)) {
                session([
                    'user_logged_in' => true,
                    'user_id'        => $admin->admin_id,
                    'user_name'      => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                    'user_email'     => $admin->admin_email ?? $admin->username,
                    'user_role'      => 'admin',
                ]);

                return response()->json([
                    'success' => true,
                    'user' => [
                        'id'       => $admin->admin_id,
                        'name'     => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                        'email'    => $admin->admin_email ?? $admin->username,
                        'username' => $admin->username,
                        'role'     => 'admin',
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Login error: ' . $e->getMessage()], 500);
        }
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $username = $request->login;

            $admin = DB::table('admin')
                        ->where(function($query) use ($username) {
                            $query->where('username', $username)
                                  ->orWhere('admin_email', $username);
                        })
                        ->first();

            if ($admin && Hash::check($request->password, $admin->password)) {
                session([
                    'user_logged_in' => true,
                    'user_id'        => $admin->admin_id,
                    'user_name'      => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                    'user_email'     => $admin->admin_email ?? $admin->username,
                    'user_role'      => 'admin',
                ]);

                return response()->json([
                    'success'  => true,
                    'message'  => 'Welcome, Admin!',
                    'redirect' => '/admin/dashboard',
                    'user' => [
                        'id'       => $admin->admin_id,
                        'name'     => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')),
                        'email'    => $admin->admin_email ?? $admin->username,
                        'username' => $admin->username,
                        'role'     => 'admin',
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid admin credentials.'], 401);

        } catch (\Exception $e) {
            Log::error('Admin login error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Login error: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        session()->flush();
        return response()->json(['success' => true, 'redirect' => '/login']);
    }

    public function user(Request $request)
    {
        if (!session('user_logged_in')) {
            return response()->json(['user' => null]);
        }

        return response()->json([
            'user' => [
                'id'    => session('user_id'),
                'name'  => session('user_name'),
                'email' => session('user_email'),
                'role'  => session('user_role'),
            ]
        ]);
    }
}