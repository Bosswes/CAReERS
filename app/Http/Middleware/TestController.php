<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function testConnection()
    {
        try {
            DB::connection()->getPdo();
            
            $students = DB::table('student_info')->count();
            $employers = DB::table('employers')->count();
            $jobs = DB::table('job_postings')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Database connected successfully',
                'stats' => [
                    'students' => $students,
                    'employers' => $employers,
                    'jobs' => $jobs
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}