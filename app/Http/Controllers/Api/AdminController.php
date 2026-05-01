<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewJobNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function getStats()
    {
        try {
            $stats = [
                'total_students'       => DB::table('student_info')->count(),
                'total_jobs'           => DB::table('job_postings')->count(),
                'pending_jobs'         => DB::table('job_postings')->where('status', 'pending')->count(),
                'approved_jobs'        => DB::table('job_postings')->where('status', 'approved')->count(),
                'rejected_jobs'        => DB::table('job_postings')->where('status', 'rejected')->count(),
                'total_recommendations'=> DB::table('recommendations')->count(),
                'total_ojt_offerings'  => DB::table('ojt_offerings')->count(),
                'total_applications'   => DB::table('applications')->count(),
                'pending_applications' => DB::table('applications')->where('status', 'pending')->count(),
                'recent_activities'    => $this->getRecentActivities()
            ];
            
            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            Log::error('Admin getStats error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching stats'], 500);
        }
    }
    
    public function getUsers(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $role = $request->get('role', '');
            
            $users = [];
            
            $studentQuery = DB::table('student_info');
            if ($search) {
                $studentQuery->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('cvsu_email', 'like', "%$search%")
                      ->orWhere('student_number', 'like', "%$search%");
                });
            }
            
            $students = $studentQuery->get();
            
            foreach ($students as $student) {
                $completion = 0;
                if ($student->first_name) $completion += 20;
                if ($student->last_name) $completion += 20;
                if ($student->program) $completion += 20;
                if ($student->general_weighted_average) $completion += 20;
                
                $hasSkills = DB::table('student_skills')->where('student_id', $student->student_number)->exists();
                if ($hasSkills) $completion += 20;
                
                $users[] = [
                    'id' => $student->student_number,
                    'name' => trim($student->first_name . ' ' . $student->last_name),
                    'email' => $student->cvsu_email,
                    'role' => 'student',
                    'status' => $student->status ?? 'active',
                    'profile_completion' => $completion,
                    'created_at' => $student->created_at
                ];
            }
            
            $adminQuery = DB::table('admin');
            if ($search) {
                $adminQuery->where(function($q) use ($search) {
                    $q->where('username', 'like', "%$search%")
                      ->orWhere('admin_email', 'like', "%$search%")
                      ->orWhere('first_name', 'like', "%$search%");
                });
            }
            
            $admins = $adminQuery->get();
            
            foreach ($admins as $admin) {
                $users[] = [
                    'id' => $admin->admin_id,
                    'name' => trim($admin->first_name . ' ' . $admin->last_name),
                    'email' => $admin->admin_email,
                    'role' => 'admin',
                    'status' => 'active',
                    'created_at' => $admin->created_at
                ];
            }
            
            if ($role) {
                $users = array_filter($users, function($u) use ($role) {
                    return $u['role'] === $role;
                });
            }
            
            $users = array_values($users);
            
            return response()->json(['success' => true, 'users' => $users]);
        } catch (\Exception $e) {
            Log::error('Admin getUsers error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching users'], 500);
        }
    }
    
    public function createUser(Request $request)
    {
        try {
            $role = $request->input('role', 'student');
            
            if ($role === 'student') {
                $validated = $request->validate([
                    'student_number' => 'required|unique:student_info,student_number',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:student_info,cvsu_email',
                    'password' => 'required|min:6'
                ]);
                
                DB::table('student_info')->insert([
                    'student_number' => $request->student_number,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'middle_name' => $request->middle_name,
                    'cvsu_email' => $request->email,
                    'password' => Hash::make($request->password),
                    'program' => $request->program,
                    'course' => $request->course,
                    'year_level' => $request->year_level,
                    'contact_number' => $request->contact_number,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else if ($role === 'admin') {
                $validated = $request->validate([
                    'username' => 'required|unique:admin,username',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:admin,admin_email',
                    'password' => 'required|min:6'
                ]);
                
                DB::table('admin')->insert([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'admin_email' => $request->email,
                    'admin_level' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json(['success' => true, 'message' => ucfirst($role) . ' created successfully']);
        } catch (\Exception $e) {
            Log::error('Admin createUser error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating user'], 500);
        }
    }
    
    public function updateUser(Request $request, $id)
    {
        try {
            $role = $request->input('role', 'student');
            
            if ($role === 'student') {
                $updateData = [];
                if ($request->has('first_name')) $updateData['first_name'] = $request->first_name;
                if ($request->has('last_name')) $updateData['last_name'] = $request->last_name;
                if ($request->has('program')) $updateData['program'] = $request->program;
                if ($request->has('year_level')) $updateData['year_level'] = $request->year_level;
                if ($request->has('general_weighted_average')) $updateData['general_weighted_average'] = $request->general_weighted_average;
                if ($request->has('password') && $request->password) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $updateData['updated_at'] = now();
                
                DB::table('student_info')->where('student_number', $id)->update($updateData);
            } else if ($role === 'admin') {
                $updateData = [];
                if ($request->has('first_name')) $updateData['first_name'] = $request->first_name;
                if ($request->has('last_name')) $updateData['last_name'] = $request->last_name;
                if ($request->has('admin_email')) $updateData['admin_email'] = $request->admin_email;
                if ($request->has('password') && $request->password) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $updateData['updated_at'] = now();
                
                DB::table('admin')->where('admin_id', $id)->update($updateData);
            }
            
            return response()->json(['success' => true, 'message' => 'User updated successfully']);
        } catch (\Exception $e) {
            Log::error('Admin updateUser error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating user'], 500);
        }
    }
    
    public function deleteUser($id)
    {
        try {
            $deleted = DB::table('student_info')->where('student_number', $id)->delete();
            if (!$deleted) {
                $deleted = DB::table('admin')->where('admin_id', $id)->delete();
            }
            
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Admin deleteUser error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting user'], 500);
        }
    }
    
    public function getJobPosts(Request $request)
    {
        try {
            $query = DB::table('job_postings')
                ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
                ->orderBy('posted_date', 'desc');
            
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('employer_name', 'like', "%$search%");
                });
            }
            
            $jobs = $query->get();
            
            foreach ($jobs as $job) {
                $job->required_skills = DB::table('required_skills')
                    ->where('job_id', $job->job_id)
                    ->pluck('skill_name');
            }
            
            return response()->json(['success' => true, 'jobs' => $jobs]);
        } catch (\Exception $e) {
            Log::error('Admin getJobPosts error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching jobs'], 500);
        }
    }
    
    public function createJobPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'employer_name' => 'required',
                'location' => 'required',
                'job_type' => 'required',
            ]);
            
            $jobId = DB::table('job_postings')->insertGetId([
                'title' => $request->title,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'responsibilities' => $request->responsibilities,
                'job_type' => $request->job_type,
                'industry' => $request->industry,
                'location' => $request->location,
                'employer_name' => $request->employer_name,
                'employer_contact' => $request->employer_contact,
                'placement_admin_email' => $request->placement_admin_email,
                'posted_by' => session('user_id'),
                'salary_range_min' => $request->salary_min,
                'salary_range_max' => $request->salary_max,
                'min_gwa' => $request->min_gwa,
                'min_year_level' => $request->min_year_level,
                'is_ojt' => $request->is_ojt ?? false,
                'posted_date' => now(),
                'deadline_date' => $request->deadline_date,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            if ($request->has('skills')) {
                $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
                foreach ($skills as $skill) {
                    if (trim($skill)) {
                        DB::table('required_skills')->insert([
                            'job_id' => $jobId,
                            'skill_name' => trim($skill),
                            'importance_level' => 'required',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            
            // Notify all students
$students = DB::table('student_info')->whereNotNull('cvsu_email')->get();
foreach ($students as $student) {
                // In-app notification
                DB::table('student_notifications')->insert([
                    'student_number' => $student->student_number,
                    'type'           => 'job',
                    'title'          => 'New Job: ' . $request->title,
                    'message'        => $request->employer_name . ' is hiring for ' . $request->title . ' (' . $request->job_type . ') in ' . $request->location,
                    'reference_id'   => $jobId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Email notification
                try {
                    \Mail::to($student->cvsu_email)->send(new \App\Mail\NewJobNotification(
                        $student->first_name . ' ' . $student->last_name,
                        $request->title,
                        $request->employer_name,
                        $request->job_type,
                        $request->location
                    ));
                } catch (\Exception $mailErr) {
                    \Log::warning('Email failed for ' . $student->cvsu_email . ': ' . $mailErr->getMessage());
                }
            }

            return response()->json(['success' => true, 'job_id' => $jobId, 'message' => 'Job posted successfully']);
        } catch (\Exception $e) {
            Log::error('Admin createJobPost error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating job'], 500);
        }
    }
    
    public function approveJob($jobId)
    {
        try {
            DB::table('job_postings')->where('job_id', $jobId)->update([
                'status' => 'approved',
                'approved_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Job approved successfully']);
        } catch (\Exception $e) {
            Log::error('Admin approveJob error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error approving job'], 500);
        }
    }
    
    public function rejectJob(Request $request, $jobId)
    {
        try {
            DB::table('job_postings')->where('job_id', $jobId)->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'rejected_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Job rejected']);
        } catch (\Exception $e) {
            Log::error('Admin rejectJob error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error rejecting job'], 500);
        }
    }
    
    public function getMonitoringStats()
    {
        try {
            $students = DB::table('student_info')->get();
            $totalStudents = $students->count();
            $completeProfiles = 0;
            $missingSkills = 0;
            $missingGWA = 0;
            $missingDegree = 0;
            $totalCompletion = 0;
            
            foreach ($students as $student) {
                $completion = 0;
                if ($student->first_name && $student->last_name) $completion += 25;
                if ($student->program) $completion += 25;
                if ($student->general_weighted_average) $completion += 25;
                
                $hasSkills = DB::table('student_skills')->where('student_id', $student->student_number)->exists();
                if ($hasSkills) {
                    $completion += 25;
                } else {
                    $missingSkills++;
                }
                
                if (!$student->general_weighted_average) $missingGWA++;
                if (!$student->program) $missingDegree++;
                
                if ($completion >= 80) $completeProfiles++;
                $totalCompletion += $completion;
            }
            
            $avgCompletion = $totalStudents > 0 ? round($totalCompletion / $totalStudents) : 0;
            
            return response()->json(['success' => true, 'stats' => [
                'total_students' => $totalStudents,
                'active_jobs' => DB::table('job_postings')->where('status', 'approved')->count(),
                'incomplete_profiles' => $totalStudents - $completeProfiles,
                'complete_profiles' => $completeProfiles,
                'avg_completion' => $avgCompletion,
                'missing_skills' => $missingSkills,
                'missing_gwa' => $missingGWA,
                'missing_degree' => $missingDegree
            ]]);
        } catch (\Exception $e) {
            Log::error('Admin getMonitoringStats error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching stats'], 500);
        }
    }
    
    public function getStudentData(Request $request)
    {
        try {
            $query = DB::table('student_info');

            // Name search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('student_number', 'like', "%$search%");
                });
            }

            // Course/Program filter
            if ($request->filled('course')) {
                $query->where('program', 'like', '%' . $request->course . '%');
            }

            // GWA min/max filter
            if ($request->filled('gwa_min')) {
                $query->where('general_weighted_average', '>=', $request->gwa_min);
            }
            if ($request->filled('gwa_max')) {
                $query->where('general_weighted_average', '<=', $request->gwa_max);
            }

            // Year level filter
            if ($request->filled('year_level')) {
                $query->where('year_level', $request->year_level);
            }

            $students = $query->limit(200)->get();
            $result = [];

            foreach ($students as $student) {
                $hasSkills = DB::table('student_skills')->where('student_id', $student->student_number)->exists();

                // Profile completion
                $completion = 0;
                if ($student->first_name && $student->last_name) $completion += 25;
                if ($student->program) $completion += 25;
                if ($student->general_weighted_average) $completion += 25;
                if ($hasSkills) $completion += 25;

                $profileStatus = $completion === 100 ? 'complete' : 'incomplete';

                // Profile status filter (after computing)
                if ($request->filled('profile_status') && $request->profile_status !== 'all') {
                    if ($request->profile_status !== $profileStatus) continue;
                }

                // Skills filter
                if ($request->filled('skill')) {
                    $hasMatchingSkill = DB::table('student_skills')
                        ->where('student_id', $student->student_number)
                        ->where('skill_name', 'like', '%' . $request->skill . '%')
                        ->exists();
                    if (!$hasMatchingSkill) continue;
                }

                // Get skills list
                $skills = DB::table('student_skills')
                    ->where('student_id', $student->student_number)
                    ->pluck('skill_name')
                    ->implode(', ');

                $result[] = [
                    'student_number' => $student->student_number,
                    'name'           => trim($student->first_name . ' ' . $student->last_name),
                    'email'          => $student->cvsu_email,
                    'program'        => $student->program,
                    'year_level'     => $student->year_level,
                    'gwa'            => $student->general_weighted_average,
                    'skills'         => $skills ?: 'None',
                    'completion'     => $completion,
                    'profile_status' => $profileStatus,
                    'has_skills'     => $hasSkills,
                    'has_gwa'        => !empty($student->general_weighted_average),
                    'updated_at'     => $student->updated_at
                ];
            }

            return response()->json(['success' => true, 'students' => $result]);
        } catch (\Exception $e) {
            Log::error('Admin getStudentData error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching data'], 500);
        }
    }
    
    public function getApplications(Request $request)
    {
        try {
            $applications = DB::table('applications')
                ->join('student_info', 'applications.student_number', '=', 'student_info.student_number')
                ->join('job_postings', 'applications.job_id', '=', 'job_postings.job_id')
                ->select(
                    'applications.*',
                    'student_info.first_name',
                    'student_info.last_name',
                    'student_info.cvsu_email',
                    'student_info.program',
                    'job_postings.title as job_title',
                    'job_postings.employer_name',
                    'job_postings.employer_contact'
                )
                ->orderBy('applications.created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'applications' => $applications]);
        } catch (\Exception $e) {
            Log::error('getApplications error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching applications'], 500);
        }
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        try {
            $status = $request->status;
            DB::table('applications')->where('application_id', $id)->update([
                'status'     => $status,
                'updated_at' => now(),
            ]);
            return response()->json(['success' => true, 'message' => 'Status updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }

    private function getRecentActivities()
    {
        $activities = [];
        
        try {
            $newStudents = DB::table('student_info')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            foreach ($newStudents as $student) {
                $activities[] = [
                    'type' => 'user',
                    'text' => "New student registered: {$student->first_name} {$student->last_name}",
                    'time' => $student->created_at ? \Carbon\Carbon::parse($student->created_at)->diffForHumans() : 'Recently',
                    'icon' => 'fa-user-graduate'
                ];
            }
            
            $newJobs = DB::table('job_postings')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            foreach ($newJobs as $job) {
                $activities[] = [
                    'type' => 'job',
                    'text' => "New job posted: {$job->title} for {$job->employer_name}",
                    'time' => $job->created_at ? \Carbon\Carbon::parse($job->created_at)->diffForHumans() : 'Recently',
                    'icon' => 'fa-briefcase'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error getting activities: ' . $e->getMessage());
        }
        
        return $activities;
    }
    
    public function getActivityFeed()
    {
        try {
            return response()->json(['success' => true, 'activities' => $this->getRecentActivities()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching activities'], 500);
        }
    }
// Idagdag itong method sa loob ng AdminController class
// (bago ang closing brace ng class, o pagkatapos ng updateApplicationStatus method)

public function deleteJobPost($jobId)
{
    try {
        DB::table('required_skills')->where('job_id', $jobId)->delete();
        DB::table('job_postings')->where('job_id', $jobId)->delete();
        return response()->json(['success' => true, 'message' => 'Job deleted successfully']);
    } catch (\Exception $e) {
        Log::error('Admin deleteJobPost error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting job'], 500);
    }
}

public function updateJobPost(Request $request, $jobId)
{
    try {
        $updateData = [];
        $fields = ['title','description','requirements','responsibilities',
                   'job_type','industry','location','employer_name',
                   'employer_contact','deadline_date','status','min_gwa','min_year_level'];
        foreach ($fields as $field) {
            if ($request->has($field)) $updateData[$field] = $request->$field;
        }
        $updateData['updated_at'] = now();
        DB::table('job_postings')->where('job_id', $jobId)->update($updateData);
        return response()->json(['success' => true, 'message' => 'Job updated successfully']);
    } catch (\Exception $e) {
        Log::error('Admin updateJobPost error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error updating job'], 500);
    }
}

public function getJobApplicants($jobId)
{
    try {
        $job = DB::table('job_postings')->where('job_id', $jobId)->first();
        if (!$job) {
            return response()->json(['success' => false, 'message' => 'Job not found'], 404);
        }

        $applicants = DB::table('applications')
            ->join('student_info', 'applications.student_number', '=', 'student_info.student_number')
            ->where('applications.job_id', $jobId)
            ->select(
                'applications.application_id',
                'applications.status',
                'applications.created_at as applied_at',
                'student_info.student_number',
                'student_info.first_name',
                'student_info.last_name',
                'student_info.cvsu_email',
                'student_info.program',
                'student_info.year_level',
                'student_info.general_weighted_average'
            )
            ->orderBy('applications.created_at', 'desc')
            ->get();

        // Attach skills per student
        foreach ($applicants as $applicant) {
            $applicant->skills = DB::table('student_skills')
                ->where('student_id', $applicant->student_number)
                ->pluck('skill_name');
        }

        return response()->json([
            'success'    => true,
            'job'        => $job,
            'applicants' => $applicants,
            'total'      => $applicants->count()
        ]);
} catch (\Exception $e) {
        Log::error('getJobApplicants error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error fetching applicants'], 500);
    }
}

public function getEventRegistrantsWithAttendance($eventId)
{
    if (!session('user_logged_in')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $event = DB::table('announcements')->where('announcement_id', $eventId)->first();

    $registrants = DB::table('event_registrants')
        ->join('student_info', 'event_registrants.student_number', '=', 'student_info.student_number')
       ->where('event_registrants.event_id', $eventId)
        ->select(
            'student_info.student_number',
            'student_info.first_name',
            'student_info.last_name',
            'student_info.program',
            'student_info.course',
            'student_info.year_level',
            'student_info.section',
            'event_registrants.created_at'
        )
        ->orderBy('student_info.last_name')
        ->get();

    $attended = DB::table('event_attendance')
        ->where('event_id', $eventId)
        ->pluck('student_number')
        ->toArray();

    $eventEnded = $event && $event->end_date && now()->gt(\Carbon\Carbon::parse($event->end_date)->endOfDay());

    $registrantsWithStatus = $registrants->map(function($r) use ($attended, $eventEnded) {
        $scanned = in_array($r->student_number, $attended);
        if ($scanned) {
            $r->attendance_status = 'present';
        } elseif ($eventEnded) {
            $r->attendance_status = 'absent';
        } else {
            $r->attendance_status = 'pending';
        }
        return $r;
    });

    return response()->json([
        'success'           => true,
        'registrants'       => $registrantsWithStatus,
        'attended'          => $attended,
        'registrant_count'  => $registrants->count(),
        'attendance_count'  => count($attended)
    ]);
}
}