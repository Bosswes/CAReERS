<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\TestController;

// Public routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::get('/user', [LoginController::class, 'user']);
Route::get('/test', [TestController::class, 'testConnection']);

// Protected routes
Route::middleware(['auth.session'])->group(function () {
    
    // Student routes
    Route::prefix('student')->group(function () {
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::put('/profile', [StudentController::class, 'update']);
        Route::get('/skills', [StudentController::class, 'skills']);
        Route::post('/skills', [StudentController::class, 'addSkill']);
        Route::delete('/skills/{skillId}', [StudentController::class, 'removeSkill']);
        Route::get('/recommendations', [StudentController::class, 'recommendations']);
        Route::post('/apply', [StudentController::class, 'applyJob']);
        Route::get('/applications', [StudentController::class, 'myApplications']);
        Route::get('/ojt-offerings', [StudentController::class, 'getOjtOfferings']);
        Route::get('/announcements', [StudentController::class, 'announcements']);
        Route::get('/references', [StudentController::class, 'getReferences']);
        Route::put('/references', [StudentController::class, 'saveReferences']);
    });
    
    // Job routes
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobController::class, 'index']);
        Route::get('/{id}', [JobController::class, 'show']);
    });
    
    // Announcement routes
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index']);
        Route::get('/{id}', [AnnouncementController::class, 'show']);
        Route::get('/{id}/qr', [AnnouncementController::class, 'getEventQR']);
        Route::post('/{id}/register', [AnnouncementController::class, 'registerStudent']);
        Route::get('/{id}/registration-status', [AnnouncementController::class, 'registrationStatus']);
Route::get('/{id}/registrants', [AnnouncementController::class, 'getRegistrants']);
    });
    
    // Admin routes
    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::get('/activity', [AdminController::class, 'getActivityFeed']);
        
        // User management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        
        // Job management
        Route::get('/jobs', [AdminController::class, 'getJobPosts']);
        Route::post('/jobs', [AdminController::class, 'createJobPost']);
        Route::put('/jobs/{jobId}', [AdminController::class, 'updateJobPost']);
        Route::delete('/jobs/{jobId}', [AdminController::class, 'deleteJobPost']);
        Route::post('/jobs/{jobId}/approve', [AdminController::class, 'approveJob']);
        Route::post('/jobs/{jobId}/reject', [AdminController::class, 'rejectJob']);
        
        // OJT Offerings
        Route::get('/ojt', [AdminController::class, 'getOjtOfferings']);
        Route::post('/ojt', [AdminController::class, 'createOjtOffering']);
        Route::put('/ojt/{offeringId}', [AdminController::class, 'updateOjtOffering']);
        Route::delete('/ojt/{offeringId}', [AdminController::class, 'deleteOjtOffering']);
        
        // Recommendations
        Route::post('/recommendations/generate', [RecommendationController::class, 'generateAllRecommendations']);
        Route::get('/recommendations', [RecommendationController::class, 'getAllRecommendations']);
        Route::post('/recommendations/{recommendationId}/sent', [RecommendationController::class, 'markAsSent']);
        
        // Monitoring
        Route::get('/monitoring/stats', [AdminController::class, 'getMonitoringStats']);
        Route::get('/monitoring/students', [AdminController::class, 'getStudentData']);
        
        // Announcements management
        Route::post('/announcements', [AnnouncementController::class, 'store']);
        Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
        Route::post('/announcements/{id}/publish', [AnnouncementController::class, 'publish']);
Route::get('/announcements/{id}/registrants', [AnnouncementController::class, 'getRegistrants']);
Route::post('/announcements/{id}/scan', [AnnouncementController::class, 'scanQR']);
        
        // Applications
        Route::get('/applications', [AdminController::class, 'getApplications']);
        Route::put('/applications/{id}/status', [AdminController::class, 'updateApplicationStatus']);
        Route::get('/jobs/{jobId}/applicants', [AdminController::class, 'getJobApplicants']);
        
        // Attendance
        Route::post('/attendance', [AdminController::class, 'recordAttendance']);
        Route::get('/attendance/{eventId}', [AdminController::class, 'getEventAttendance']);
        Route::get('/announcements/{id}/registrants-attendance', [AdminController::class, 'getEventRegistrantsWithAttendance']);
    });
});