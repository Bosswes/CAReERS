<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin table
        Schema::create('admin', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('admin_email')->nullable();
            $table->enum('admin_level', ['super_admin', 'admin', 'moderator'])->default('admin');
            $table->boolean('can_manage_users')->default(true);
            $table->boolean('can_manage_jobs')->default(true);
            $table->boolean('can_manage_announcements')->default(true);
            $table->boolean('can_view_reports')->default(true);
            $table->timestamp('last_activity')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Student Info table
        Schema::create('student_info', function (Blueprint $table) {
            $table->string('student_number')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('cvsu_email')->nullable()->unique();
            $table->string('password');
            $table->string('program')->nullable();
            $table->string('course')->nullable();
            $table->tinyInteger('year_level')->nullable();
            $table->decimal('general_weighted_average', 3, 2)->nullable();
            $table->string('contact_number')->nullable();
            $table->string('section')->nullable();
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // Student Skills table
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id('student_skill_id');
            $table->string('student_id');
            $table->string('skill_name');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->integer('years_experience')->default(0);
            $table->timestamps();
            
            $table->foreign('student_id')->references('student_number')->on('student_info')->onDelete('cascade');
            $table->unique(['student_id', 'skill_name']);
        });

        // Job Postings table
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->string('job_type');
            $table->string('industry')->nullable();
            $table->string('location');
            $table->string('employer_name');
            $table->string('employer_contact')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->decimal('salary_range_min', 10, 2)->nullable();
            $table->decimal('salary_range_max', 10, 2)->nullable();
            $table->decimal('min_gwa', 3, 2)->nullable();
            $table->string('min_year_level')->nullable();
            $table->boolean('is_ojt')->default(false);
            $table->date('posted_date');
            $table->date('deadline_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->timestamps();
            
            $table->foreign('posted_by')->references('admin_id')->on('admin')->onDelete('set null');
        });

        // Required Skills table
        Schema::create('required_skills', function (Blueprint $table) {
            $table->id('required_skill_id');
            $table->unsignedBigInteger('job_id');
            $table->string('skill_name');
            $table->enum('importance_level', ['required', 'preferred'])->default('required');
            $table->timestamps();
            
            $table->foreign('job_id')->references('job_id')->on('job_postings')->onDelete('cascade');
            $table->unique(['job_id', 'skill_name']);
        });

        // Applications table (for tracking recommendations sent)
        Schema::create('applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->string('student_number');
            $table->unsignedBigInteger('job_id');
            $table->enum('status', ['pending', 'sent', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('application_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('student_number')->references('student_number')->on('student_info')->onDelete('cascade');
            $table->foreign('job_id')->references('job_id')->on('job_postings')->onDelete('cascade');
        });

        // Announcements table
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id');
            $table->string('title');
            $table->text('content');
            $table->string('announcement_type'); // event, news, deadline, general
            $table->enum('target_audience', ['all', 'students', 'admins'])->default('all');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->foreign('created_by')->references('admin_id')->on('admin')->onDelete('set null');
        });

        // OJT Offerings table
        Schema::create('ojt_offerings', function (Blueprint $table) {
            $table->id('ojt_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->string('duration')->nullable();
            $table->integer('slots')->default(1);
            $table->enum('status', ['open', 'closed', 'filled'])->default('open');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('admin_id')->on('admin')->onDelete('set null');
        });

        // Event Attendance table
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('event_id');
            $table->string('student_number');
            $table->timestamp('attendance_time');
            $table->string('qr_code')->nullable();
            $table->timestamps();
            
            $table->foreign('event_id')->references('announcement_id')->on('announcements')->onDelete('cascade');
            $table->foreign('student_number')->references('student_number')->on('student_info')->onDelete('cascade');
        });

        // Recommendations table
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id('recommendation_id');
            $table->string('student_number');
            $table->unsignedBigInteger('job_id');
            $table->decimal('match_score', 5, 2);
            $table->integer('rank_position');
            $table->timestamp('generated_at');
            $table->enum('status', ['pending', 'sent', 'accepted'])->default('pending');
            $table->timestamps();
            
            $table->foreign('student_number')->references('student_number')->on('student_info')->onDelete('cascade');
            $table->foreign('job_id')->references('job_id')->on('job_postings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('event_attendance');
        Schema::dropIfExists('ojt_offerings');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('required_skills');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('student_skills');
        Schema::dropIfExists('student_info');
        Schema::dropIfExists('admin');
    }
};