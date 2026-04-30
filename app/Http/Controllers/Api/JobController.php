<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewJobNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('job_postings')
                  ->where('status', 'approved')
                  ->orderBy('posted_date', 'desc');
        
        if ($request->has('industry') && $request->industry) {
            $query->where('industry', $request->industry);
        }
        
        if ($request->has('job_type') && $request->job_type) {
            $query->where('job_type', $request->job_type);
        }
        
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        
        $jobs = $query->get();
        
        return response()->json([
            'success' => true,
            'jobs' => $jobs
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string',
            'employer_name'   => 'required|string',
            'job_type'        => 'required|string',
            'location'        => 'required|string',
            'min_gpa'         => 'nullable|numeric',
            'min_year_level'  => 'nullable|string',
            'required_skills' => 'nullable|string',
            'deadline'        => 'nullable|date',
        ]);

        $jobId = DB::table('job_postings')->insertGetId([
            'title'          => $request->title,
            'employer_name'  => $request->employer_name,
            'job_type'       => $request->job_type,
            'location'       => $request->location,
            'min_gpa'        => $request->min_gpa,
            'min_year_level' => $request->min_year_level,
            'deadline'       => $request->deadline,
            'status'         => 'approved',
            'posted_date'    => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        if ($request->required_skills) {
            $skills = explode(',', $request->required_skills);
            foreach ($skills as $skill) {
                DB::table('required_skills')->insert([
                    'job_id'     => $jobId,
                    'skill_name' => trim($skill),
                ]);
            }
        }

        $students = DB::table('student_info')
                      ->whereNotNull('cvsu_email')
                      ->get();

        foreach ($students as $student) {
            try {
                Mail::to($student->cvsu_email)->send(new NewJobNotification(
                    studentName:  ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    jobTitle:     $request->title,
                    employerName: $request->employer_name,
                    jobType:      $request->job_type,
                    location:     $request->location,
                ));
            } catch (\Exception $e) {
                Log::error('Email failed for ' . $student->cvsu_email . ': ' . $e->getMessage());
            }

            DB::table('student_notifications')->insert([
                'student_number' => $student->student_number,
                'type'           => 'job',
                'title'          => 'New Job: ' . $request->title,
                'message'        => $request->employer_name . ' is hiring! Check the new job posting.',
                'reference_id'   => $jobId,
                'is_read'        => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Job posted and students notified!',
            'job_id'  => $jobId,
        ]);
    }

    public function show($id)
    {
        $job = DB::table('job_postings')
                ->where('job_id', $id)
                ->first();
        
        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }
        
        $skills = DB::table('required_skills')
                   ->where('job_id', $id)
                   ->get();
        
        $job->required_skills = $skills;
        
        return response()->json([
            'success' => true,
            'job' => $job
        ]);
    }
}