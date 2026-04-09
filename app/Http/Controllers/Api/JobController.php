<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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