<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_logged_in') || session('user_role') !== 'student') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Student access required.'], 401);
            }
            return redirect('/');
        }
        
        return $next($request);
    }
}