<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (Coordinator)
        DB::table('admin')->insert([
            'admin_id' => 1,
            'username' => 'coordinator',
            'password' => Hash::make('coordinator123'),
            'first_name' => 'Job',
            'last_name' => 'Placement Coordinator',
            'admin_email' => 'coordinator@cvsu.edu.ph',
            'admin_level' => 'admin',
            'can_manage_users' => true,
            'can_manage_jobs' => true,
            'can_manage_announcements' => true,
            'can_view_reports' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Super Admin
        DB::table('admin')->insert([
            'admin_id' => 2,
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'admin_email' => 'admin@cvsu.edu.ph',
            'admin_level' => 'super_admin',
            'can_manage_users' => true,
            'can_manage_jobs' => true,
            'can_manage_announcements' => true,
            'can_view_reports' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Student
        DB::table('student_info')->insert([
            'student_number' => '202400001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Santos',
            'cvsu_email' => 'juan.delacruz@cvsu.edu.ph',
            'password' => Hash::make('student123'),
            'program' => 'Bachelor of Science in Computer Science',
            'course' => 'BSCS',
            'year_level' => 4,
            'general_weighted_average' => 1.75,
            'contact_number' => '09123456789',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Student Skills
        DB::table('student_skills')->insert([
            [
                'student_id' => '202400001',
                'skill_name' => 'PHP',
                'proficiency_level' => 'intermediate',
                'years_experience' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'student_id' => '202400001',
                'skill_name' => 'Laravel',
                'proficiency_level' => 'intermediate',
                'years_experience' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'student_id' => '202400001',
                'skill_name' => 'MySQL',
                'proficiency_level' => 'advanced',
                'years_experience' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'student_id' => '202400001',
                'skill_name' => 'JavaScript',
                'proficiency_level' => 'intermediate',
                'years_experience' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Job Posting
        DB::table('job_postings')->insert([
            'job_id' => 1,
            'title' => 'Junior Software Developer',
            'description' => 'We are looking for a passionate junior developer to join our team.',
            'requirements' => 'Knowledge in PHP, JavaScript, MySQL, and Laravel framework.',
            'responsibilities' => 'Develop and maintain web applications.',
            'job_type' => 'full-time',
            'industry' => 'IT',
            'location' => 'Makati City',
            'employer_name' => 'Tech Solutions Inc.',
            'employer_contact' => 'hr@techsolutions.com',
            'posted_by' => 1,
            'salary_range_min' => 25000,
            'salary_range_max' => 35000,
            'min_gwa' => 2.5,
            'min_year_level' => '4th Year',
            'is_ojt' => false,
            'posted_date' => now(),
            'deadline_date' => now()->addDays(30),
            'status' => 'approved',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Required Skills
        DB::table('required_skills')->insert([
            [
                'job_id' => 1,
                'skill_name' => 'PHP',
                'importance_level' => 'required',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'job_id' => 1,
                'skill_name' => 'Laravel',
                'importance_level' => 'required',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'job_id' => 1,
                'skill_name' => 'MySQL',
                'importance_level' => 'required',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // OJT Offering
        DB::table('ojt_offerings')->insert([
            'ojt_id' => 1,
            'title' => 'Web Development Intern',
            'description' => 'Looking for IT/CS students for internship program.',
            'requirements' => 'Knowledge in HTML, CSS, JavaScript.',
            'company_name' => 'Digital Innovations Inc.',
            'location' => 'BGC, Taguig',
            'duration' => '300 hours',
            'slots' => 5,
            'status' => 'open',
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Announcements
        DB::table('announcements')->insert([
            [
                'announcement_id' => 1,
                'title' => 'Annual Career Fair 2026',
                'content' => 'Join us for the annual career fair on March 25, 2026 at the CvSU Carmona Gymnasium.',
                'announcement_type' => 'event',
                'target_audience' => 'all',
                'start_date' => '2026-03-25',
                'end_date' => '2026-03-25',
                'location' => 'CvSU Carmona Gymnasium',
                'is_published' => true,
                'created_by' => 1,
                'views_count' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'announcement_id' => 2,
                'title' => 'Resume Writing Workshop',
                'content' => 'Learn how to create an effective resume that stands out to employers.',
                'announcement_type' => 'event',
                'target_audience' => 'students',
                'start_date' => '2026-03-10',
                'end_date' => '2026-03-10',
                'location' => 'IT Building Room 101',
                'is_published' => true,
                'created_by' => 1,
                'views_count' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}