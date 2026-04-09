<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function profile(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $studentId = session('user_id');
        
        $student = DB::table('student_info')
                    ->where('student_number', $studentId)
                    ->first();
        
        $skills = DB::table('student_skills')
                    ->where('student_id', $studentId)
                    ->pluck('skill_name')
                    ->toArray();
        
        $student->skills = $skills;
        
        return response()->json([
            'success' => true,
            'profile' => $student
        ]);
    }
    
    public function update(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $studentId = session('user_id');
        
        $updateData = [];
        
        if ($request->has('first_name')) {
            $updateData['first_name'] = $request->first_name;
        }
        if ($request->has('last_name')) {
            $updateData['last_name'] = $request->last_name;
        }
        if ($request->has('middle_name')) {
            $updateData['middle_name'] = $request->middle_name;
        }
        if ($request->has('contact_number')) {
            $updateData['contact_number'] = $request->contact_number;
        }
        if ($request->has('program')) {
            $updateData['program'] = $request->program;
        }
        if ($request->has('course')) {
            $updateData['course'] = $request->course;
        }
        if ($request->has('year_level')) {
            $updateData['year_level'] = $request->year_level;
        }
        if ($request->has('general_weighted_average')) {
            $updateData['general_weighted_average'] = $request->general_weighted_average;
        }
        if ($request->has('section')) {
            $updateData['section'] = $request->section;
        }
        if ($request->has('profile_photo')) {
            $updateData['profile_photo'] = $request->profile_photo; // base64 string
        }
        
        // Handle resume upload
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = 'resume_' . $studentId . '_' . time() . '.pdf';
            $file->move(storage_path('app/public/resumes'), $filename);
            $updateData['resume_path'] = 'resumes/' . $filename;
        }

        if (!empty($updateData)) {
            DB::table('student_info')
                ->where('student_number', $studentId)
                ->update($updateData);
        }
        
        if ($request->has('skills')) {
            DB::table('student_skills')->where('student_id', $studentId)->delete();
            
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            foreach ($skills as $skill) {
                if (trim($skill)) {
                    DB::table('student_skills')->insert([
                        'student_id' => $studentId,
                        'skill_name' => trim($skill),
                        'proficiency_level' => 'beginner',
                        'years_experience' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }
    
    public function skills(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $studentId = session('user_id');
        
        $skills = DB::table('student_skills')
                   ->where('student_id', $studentId)
                   ->get();
        
        return response()->json(['success' => true, 'skills' => $skills]);
    }
    
    public function addSkill(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $studentId = session('user_id');
        
        $request->validate([
            'skill_name' => 'required|string'
        ]);
        
        $exists = DB::table('student_skills')
            ->where('student_id', $studentId)
            ->where('skill_name', $request->skill_name)
            ->exists();
        
        if (!$exists) {
            DB::table('student_skills')->insert([
                'student_id' => $studentId,
                'skill_name' => $request->skill_name,
                'proficiency_level' => $request->proficiency_level ?? 'beginner',
                'years_experience' => $request->years_experience ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Skill added successfully']);
    }
    
    public function removeSkill($skillId)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $studentId = session('user_id');
        
        DB::table('student_skills')
            ->where('student_skill_id', $skillId)
            ->where('student_id', $studentId)
            ->delete();
        
        return response()->json(['success' => true, 'message' => 'Skill removed successfully']);
    }
    
    public function recommendations(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $studentId = session('user_id');

        // Get student info (course and town/city for location sorting)
        $student = DB::table('student_info')->where('student_number', $studentId)->first();
        $studentCourse = strtoupper(trim($student->program ?? $student->course ?? ''));
        $studentTown = strtolower(trim($student->town ?? ''));

        // Course-to-industry/keyword mapping (matched to full degree names)
        $courseAliases = [
            'BSCS'  => 'BS COMPUTER SCIENCE',
            'BSIT'  => 'BS INFORMATION TECHNOLOGY',
            'BSCOE' => 'BS COMPUTER ENGINEERING',
            'BSED'  => 'BACHELOR OF SECONDARY EDUCATION',
            'BSBM'  => 'BS BUSINESS MANAGEMENT',
            'BSHM'  => 'BS HOSPITALITY MANAGEMENT',
        ];
        $normalizedCourse = $courseAliases[$studentCourse] ?? $studentCourse;

        // Industry-based course mapping
$courseIndustryMap = [
    'BS COMPUTER SCIENCE'             => ['IT'],
    'BS INFORMATION TECHNOLOGY'       => ['IT'],
    'BS COMPUTER ENGINEERING'         => ['IT'],
    'BACHELOR OF SECONDARY EDUCATION' => ['Education'],
    'BS BUSINESS MANAGEMENT'          => ['Finance'],
    'BS HOSPITALITY MANAGEMENT'       => ['Hospitality', 'Healthcare'],
    'BS INDUSTRIAL TECHNOLOGY'        => ['Engineering'],
    'BS NURSING'                      => ['Healthcare'],
    'BS MEDICAL TECHNOLOGY'           => ['Healthcare'],
];

$matchedIndustries = $courseIndustryMap[$normalizedCourse] ?? [];
$courseKeywords = []; // no longer used for primary filter
        

        // Get student skills
        $studentSkills = DB::table('student_skills')
            ->where('student_id', $studentId)
            ->pluck('skill_name')
            ->map(fn($s) => strtolower(trim($s)))
            ->toArray();

        // Get all approved jobs
        $jobs = DB::table('job_postings')
            ->where('status', 'approved')
            ->get();

        // Filter to course-related jobs only (if mapping exists), then score & sort
        $recommendations = $jobs->filter(function($job) use ($matchedIndustries) {
    if (empty($matchedIndustries)) return true; // show all if no mapping

    return in_array($job->industry, $matchedIndustries);
        })->map(function($job) use ($studentSkills, $studentTown, $student) {
            // Skill match score
            $requiredSkills = DB::table('required_skills')
                ->where('job_id', $job->job_id)
                ->pluck('skill_name')
                ->map(fn($s) => strtolower(trim($s)))
                ->toArray();

            // === SKILLS SCORE (60%) ===
            $skillScore = 0;
            if (!empty($requiredSkills) && !empty($studentSkills)) {
                $matched = count(array_intersect($studentSkills, $requiredSkills));
                $skillScore = round(($matched / max(count($requiredSkills), 1)) * 60);
            } elseif (empty($requiredSkills)) {
                $skillScore = 30; // partial credit if no required skills listed
            }

            // === GWA SCORE (25%) ===
            $gwaRaw = $student->general_weighted_average ?? null;
            $gwa = $gwaRaw !== null ? (float) $gwaRaw : null;
            if ($gwa === null) {
                $gwaScore = 10; // neutral if no GWA entered
            } elseif ($gwa <= 1.5) {
                $gwaScore = 25;
            } elseif ($gwa <= 2.0) {
                $gwaScore = 20;
            } elseif ($gwa <= 2.5) {
                $gwaScore = 15;
            } elseif ($gwa <= 3.0) {
                $gwaScore = 10;
            } else {
                $gwaScore = 5;
            }

            // === YEAR LEVEL SCORE (15%) ===
            $studentYear = (int) ($student->year_level ?? 0);
            $requiredYear = (int) ($job->min_year_level ?? 0);
            // already correct, no change needed
            if ($studentYear === 0) {
                $yearScore = 8; // neutral if no year level entered
            } elseif ($requiredYear === 0 || $studentYear >= $requiredYear) {
                $yearScore = 15;
            } else {
                $yearScore = 0;
            }
            // === TOTAL MATCH SCORE ===
            $matchScore = max(10, min(99, $skillScore + $gwaScore + $yearScore));

            // Location proximity score (1 = same city, 0 = different)
            $jobLocation = strtolower($job->location ?? '');
            $locationScore = (!empty($studentTown) && str_contains($jobLocation, $studentTown)) ? 1 : 0;

            $job->match_score = $matchScore;
            $job->location_score = $locationScore;
            $job->required_skills = $requiredSkills;
            return $job;
        })->sortBy([
            fn($a, $b) => $b->location_score <=> $a->location_score, // nearest first
            fn($a, $b) => $b->match_score <=> $a->match_score,       // then highest match
        ])->values();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }
    
    public function applyJob(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $studentId = session('user_id');
        $jobId = $request->job_id;

        if (!$jobId) {
            return response()->json(['success' => false, 'message' => 'Job ID required'], 400);
        }

        // Check if already applied
        $exists = DB::table('applications')
            ->where('student_number', $studentId)
            ->where('job_id', $jobId)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You have already applied for this job.']);
        }

        // Get job details
        $job = DB::table('job_postings')->where('job_id', $jobId)->first();
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job not found.'], 404);
        }

        // Get student details
        $student = DB::table('student_info')->where('student_number', $studentId)->first();

        // Save application
        DB::table('applications')->insert([
            'student_number'   => $studentId,
            'job_id'           => $jobId,
            'status'           => 'pending',
            'application_date' => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Increment applications count on job posting
        DB::table('job_postings')->where('job_id', $jobId)->increment('applications_count');

        // Save resume PDF from base64 if provided by browser
        $resumePath = null;
        if ($request->has('resume_base64') && $request->resume_base64) {
            try {
                $pdfData = base64_decode($request->resume_base64);
                $filename = 'resume_' . $studentId . '_' . time() . '.pdf';
                $dir = storage_path('app/public/resumes');
                if (!file_exists($dir)) mkdir($dir, 0755, true);
                file_put_contents($dir . '/' . $filename, $pdfData);
                $resumePath = $dir . '/' . $filename;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Resume save error: ' . $e->getMessage());
            }
        }

        // Send email to employer if contact email exists
        if ($job->employer_contact && filter_var($job->employer_contact, FILTER_VALIDATE_EMAIL)) {
            try {
                $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));

                \Illuminate\Support\Facades\Mail::raw(
                    "Hello,\n\nA student has applied for your job posting.\n\n" .
                    "Job Title: {$job->title}\n" .
                    "Student Name: {$studentName}\n" .
                    "Student Email: " . ($student->cvsu_email ?? 'N/A') . "\n" .
                    "Student Number: {$studentId}\n" .
                    "Program: " . ($student->program ?? 'N/A') . "\n" .
                    "Year Level: " . ($student->year_level ?? 'N/A') . "\n\n" .
                    "Please log in to the CAReERS system to review this application.\n\n" .
                    "CAReERS - CvSU Carmona Job Recommendation System",
                    function($message) use ($job, $resumePath) {
                        $message->to($job->employer_contact)
                                ->subject('New Job Application - ' . $job->title);
                        if ($resumePath && file_exists($resumePath)) {
                            $message->attach($resumePath, [
                                'as' => 'Student_Resume.pdf',
                                'mime' => 'application/pdf'
                            ]);
                        }
                    }
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Email failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully! The employer has been notified.'
        ]);
    }

    public function myApplications(Request $request)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $studentId = session('user_id');

        $applications = DB::table('applications')
            ->join('job_postings', 'applications.job_id', '=', 'job_postings.job_id')
            ->where('applications.student_number', $studentId)
            ->select(
                'applications.*',
                'job_postings.title',
                'job_postings.employer_name',
                'job_postings.location',
                'job_postings.job_type'
            )
            ->orderBy('applications.created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'applications' => $applications]);
    }

    public function getOjtOfferings()
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $offerings = DB::table('ojt_offerings')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'offerings' => $offerings
        ]);
    }
    
    public function announcements()
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $announcements = DB::table('announcements')
            ->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->whereIn('target_audience', ['all', 'students'])
            ->orderBy('start_date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'announcements' => $announcements
        ]);
    }
}