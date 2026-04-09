<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'firstName'     => 'required|string|max:100',
            'lastName'      => 'required|string|max:100',
            'email'         => 'required|email|unique:student_info,cvsu_email',
            'studentNumber' => 'required|digits:9|unique:student_info,student_number',
            'password'      => 'required|string|min:8',
        ]);

        try {
            DB::table('student_info')->insert([
                'student_number' => $request->studentNumber,
                'first_name'     => $request->firstName,
                'last_name'      => $request->lastName,
                'middle_name'    => $request->middleName ?? null,
                'cvsu_email'     => $request->email,
                'password'       => Hash::make($request->password),
                'program'        => $request->course ?? null,
                'course'         => $request->course ?? null,
                'year_level'     => $request->yearLevel ?? null,
                'section'        => $request->section ?? null,
                'contact_number' => $request->cellphoneNo ?? null,
                'town'           => $request->town ?? null,
                // Educational Background
                'shs_school'     => $request->shsSchool ?? null,
                'shs_year_grad'  => $request->shsYearGrad ?? null,
                'shs_type'       => $request->shsType ?? null,
                'hs_school'      => $request->hsSchool ?? null,
                'hs_year_grad'   => $request->hsYearGrad ?? null,
                'hs_type'        => $request->hsType ?? null,
                'elem_school'    => $request->elemSchool ?? null,
                'elem_year_grad' => $request->elemYearGrad ?? null,
                'elem_type'      => $request->elemType ?? null,
                'status'         => 'active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Redirecting to login...',
            ]);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}