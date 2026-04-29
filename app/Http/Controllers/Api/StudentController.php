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
            $updateData['profile_photo'] = $request->profile_photo;
        }
        // New personal detail fields
        if ($request->has('birth_date')) {
            $updateData['birth_date'] = $request->birth_date;
        }
        if ($request->has('birth_place')) {
            $updateData['birth_place'] = $request->birth_place;
        }
        if ($request->has('full_address')) {
            $updateData['full_address'] = $request->full_address;
        }
        
        // Character references
        if ($request->has('ref1_name'))     $updateData['ref1_name']     = $request->ref1_name;
        if ($request->has('ref1_position')) $updateData['ref1_position'] = $request->ref1_position;
        if ($request->has('ref1_company'))  $updateData['ref1_company']  = $request->ref1_company;
        if ($request->has('ref1_contact'))  $updateData['ref1_contact']  = $request->ref1_contact;
        if ($request->has('ref2_name'))     $updateData['ref2_name']     = $request->ref2_name;
        if ($request->has('ref2_position')) $updateData['ref2_position'] = $request->ref2_position;
        if ($request->has('ref2_company'))  $updateData['ref2_company']  = $request->ref2_company;
        if ($request->has('ref2_contact'))  $updateData['ref2_contact']  = $request->ref2_contact;

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

        $student = DB::table('student_info')->where('student_number', $studentId)->first();
        $studentCourse = strtoupper(trim($student->program ?? $student->course ?? ''));
        $studentTown = strtolower(trim($student->town ?? ''));

        $courseAliases = [
            'BSCS'  => 'BS COMPUTER SCIENCE',
            'BSIT'  => 'BS INFORMATION TECHNOLOGY',
            'BSCOE' => 'BS COMPUTER ENGINEERING',
            'BSED'  => 'BACHELOR OF SECONDARY EDUCATION',
            'BSBM'  => 'BS BUSINESS MANAGEMENT',
            'BSHM'  => 'BS HOSPITALITY MANAGEMENT',
        ];
        $normalizedCourse = $courseAliases[$studentCourse] ?? $studentCourse;

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

        $studentSkills = DB::table('student_skills')
            ->where('student_id', $studentId)
            ->pluck('skill_name')
            ->map(fn($s) => strtolower(trim($s)))
            ->toArray();

        $jobs = DB::table('job_postings')
            ->where('status', 'approved')
            ->get();

        $recommendations = $jobs->filter(function($job) use ($matchedIndustries) {
            if (empty($matchedIndustries)) return true;
            return in_array($job->industry, $matchedIndustries);
        })->map(function($job) use ($studentSkills, $studentTown, $student) {
            $requiredSkills = DB::table('required_skills')
                ->where('job_id', $job->job_id)
                ->pluck('skill_name')
                ->map(fn($s) => strtolower(trim($s)))
                ->toArray();

            $skillScore = 0;
            if (!empty($requiredSkills) && !empty($studentSkills)) {
                $matched = count(array_intersect($studentSkills, $requiredSkills));
                $skillScore = round(($matched / max(count($requiredSkills), 1)) * 60);
            } elseif (empty($requiredSkills)) {
                $skillScore = 30;
            }

            $gwaRaw = $student->general_weighted_average ?? null;
            $gwa = $gwaRaw !== null ? (float) $gwaRaw : null;
            if ($gwa === null) {
                $gwaScore = 10;
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

            $studentYear = (int) ($student->year_level ?? 0);
            $requiredYear = (int) ($job->min_year_level ?? 0);
            if ($studentYear === 0) {
                $yearScore = 8;
            } elseif ($requiredYear === 0 || $studentYear >= $requiredYear) {
                $yearScore = 15;
            } else {
                $yearScore = 0;
            }

            $matchScore = max(10, min(99, $skillScore + $gwaScore + $yearScore));

            $jobLocation = strtolower($job->location ?? '');
            $locationScore = (!empty($studentTown) && str_contains($jobLocation, $studentTown)) ? 1 : 0;

            $job->match_score = $matchScore;
            $job->location_score = $locationScore;
            $job->required_skills = $requiredSkills;
            return $job;
        })->sortBy([
            fn($a, $b) => $b->location_score <=> $a->location_score,
            fn($a, $b) => $b->match_score <=> $a->match_score,
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

        $exists = DB::table('applications')
            ->where('student_number', $studentId)
            ->where('job_id', $jobId)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You have already applied for this job.']);
        }

        $job = DB::table('job_postings')->where('job_id', $jobId)->first();
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job not found.'], 404);
        }

        $student = DB::table('student_info')->where('student_number', $studentId)->first();

        DB::table('applications')->insert([
            'student_number'   => $studentId,
            'job_id'           => $jobId,
            'status'           => 'pending',
            'application_date' => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('job_postings')->where('job_id', $jobId)->increment('applications_count');

        // Save resume HTML as PDF if provided
        $resumePath = null;
        if ($request->has('resume_html') && $request->resume_html) {
            try {
                $resumeHtml = $request->resume_html;
                $filename = 'resume_' . $studentId . '_' . time() . '.html';
                $dir = storage_path('app/public/resumes');
                if (!file_exists($dir)) mkdir($dir, 0755, true);
                // Store HTML version for email attachment reference
                $htmlPath = $dir . '/' . $filename;
                file_put_contents($htmlPath, $resumeHtml);
                $resumePath = $htmlPath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Resume save error: ' . $e->getMessage());
            }
        } elseif ($request->has('resume_base64') && $request->resume_base64) {
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

        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));

        // Build detailed email body with new personal fields
        $birthDate   = !empty($student->birth_date)   ? date('F d, Y', strtotime($student->birth_date)) : 'N/A';
        $birthPlace  = $student->birth_place  ?? 'N/A';
        $fullAddress = $student->full_address ?? 'N/A';

        $emailBody =
            "Hello,\n\nA student has applied for your job posting.\n\n" .
            "=== JOB DETAILS ===\n" .
            "Job Title: {$job->title}\n\n" .
            "=== STUDENT INFORMATION ===\n" .
            "Full Name:      {$studentName}\n" .
            "Email:          " . ($student->cvsu_email ?? 'N/A') . "\n" .
            "Contact Number: " . ($student->contact_number ?? 'N/A') . "\n" .
            "Date of Birth:  {$birthDate}\n" .
            "Birth Place:    {$birthPlace}\n" .
            "Address:        {$fullAddress}\n\n" .
            "=== ACADEMIC DETAILS ===\n" .
            "Program:        " . ($student->program ?? 'N/A') . "\n" .
            "Year Level:     " . ($student->year_level ?? 'N/A') . "\n" .
            "Section:        " . ($student->section ?? 'N/A') . "\n" .
            "GWA:            " . ($student->general_weighted_average ?? 'N/A') . "\n\n" .
            "Please log in to the CAReERS system to review this application.\n\n" .
            "CAReERS - CvSU Carmona Job Recommendation System";

        if ($job->employer_contact && filter_var($job->employer_contact, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    $emailBody,
                    function($message) use ($job, $resumePath) {
                        $message->to($job->employer_contact)
                                ->subject('New Job Application - ' . $job->title);
                        if ($resumePath && file_exists($resumePath)) {
                            $isPdf = str_ends_with($resumePath, '.pdf');
                            $message->attach($resumePath, [
                                'as'   => $isPdf ? 'Student_Resume.pdf' : 'Student_Resume.html',
                                'mime' => $isPdf ? 'application/pdf' : 'text/html',
                            ]);
                        }
                    }
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Email to employer failed: ' . $e->getMessage());
            }
        }

        if (!empty($job->placement_admin_email) && filter_var($job->placement_admin_email, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "[PLACEMENT ADMIN COPY]\n\n" . $emailBody,
                    function($message) use ($job, $resumePath) {
                        $message->to($job->placement_admin_email)
                                ->subject('[Admin Copy] New Application - ' . $job->title);
                        if ($resumePath && file_exists($resumePath)) {
                            $isPdf = str_ends_with($resumePath, '.pdf');
                            $message->attach($resumePath, [
                                'as'   => $isPdf ? 'Student_Resume.pdf' : 'Student_Resume.html',
                                'mime' => $isPdf ? 'application/pdf' : 'text/html',
                            ]);
                        }
                    }
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Email to placement admin failed: ' . $e->getMessage());
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
    public function getNotifications()
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $student = DB::table('student_info')->where('student_number', session('user_id'))->first();
        $notifications = DB::table('student_notifications')
            ->where('student_number', $student->student_number)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        $unreadCount = DB::table('student_notifications')
            ->where('student_number', $student->student_number)
            ->where('is_read', false)
            ->count();
        return response()->json(['success' => true, 'notifications' => $notifications, 'unread_count' => $unreadCount]);
    }

    public function markNotificationRead($id)
    {
        DB::table('student_notifications')->where('id', $id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        if (!session('user_logged_in')) return response()->json(['error' => 'Unauthorized'], 401);
        $student = DB::table('student_info')->where('student_number', session('user_id'))->first();
        DB::table('student_notifications')
            ->where('student_number', $student->student_number)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}