<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    if (session('user_logged_in')) {
        $role = session('user_role');
        return redirect($role === 'admin' ? '/admin/dashboard' : '/student/dashboard');
    }
    return redirect('/login');
});

Route::get('/app', function () {
    return view('app');
});

Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        $students = DB::table('student_info')->count();
        $jobs = DB::table('job_postings')->count();
        $announcements = DB::table('announcements')->count();
        $recommendations = DB::table('recommendations')->count();
        
        return response()->json([
            'connected' => true,
            'students' => $students,
            'jobs' => $jobs,
            'announcements' => $announcements,
            'recommendations' => $recommendations
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'connected' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// ─── Auth Routes ────────────────────────────────────────────────────────────
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::post('/admin/login', [LoginController::class, 'adminLogin']);

use App\Http\Controllers\Auth\ForgotPasswordController;
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::get('/reset-password', function () { return view('auth.login'); });
// ────────────────────────────────────────────────────────────────────────────

// Attendance routes
Route::get('/attendance/scan/{id}', [App\Http\Controllers\Api\AttendanceController::class, 'scan']);
Route::post('/attendance/log', [App\Http\Controllers\Api\AttendanceController::class, 'log']);
Route::get('/attendance/print/{id}', [App\Http\Controllers\Api\AttendanceController::class, 'printView']);

// Catch all route for SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!login|register|admin|logout).*$');