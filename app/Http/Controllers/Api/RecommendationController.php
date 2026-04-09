<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function generateAllRecommendations()
    {
        try {
            DB::table('recommendations')->truncate();
            
            $students = DB::table('student_info')->get();
            $jobs = DB::table('job_postings')->where('status', 'approved')->get();
            
            $allRecommendations = [];
            
            foreach ($students as $student) {
                $studentSkills = DB::table('student_skills')
                    ->where('student_id', $student->student_number)
                    ->pluck('skill_name')
                    ->toArray();
                
                $jobScores = [];
                
                foreach ($jobs as $job) {
                    $jobSkills = DB::table('required_skills')
                        ->where('job_id', $job->job_id)
                        ->pluck('skill_name')
                        ->toArray();
                    
                    $score = 50;
                    
                    if (!empty($jobSkills)) {
                        $matchedSkills = array_intersect($studentSkills, $jobSkills);
                        $score = (count($matchedSkills) / count($jobSkills)) * 100;
                    }
                    
                    if ($job->min_gwa && $student->general_weighted_average) {
                        if ($student->general_weighted_average <= $job->min_gwa) {
                            $score += 10;
                        }
                    }
                    
                    $score = min(100, round($score));
                    
                    if ($score >= 50) {
                        $jobScores[$job->job_id] = $score;
                    }
                }
                
                arsort($jobScores);
                $topJobs = array_slice($jobScores, 0, 10, true);
                
                $rank = 1;
                foreach ($topJobs as $jobId => $score) {
                    $allRecommendations[] = [
                        'student_number' => $student->student_number,
                        'job_id' => $jobId,
                        'match_score' => $score,
                        'rank_position' => $rank,
                        'generated_at' => now(),
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $rank++;
                }
            }
            
            if (!empty($allRecommendations)) {
                DB::table('recommendations')->insert($allRecommendations);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Recommendations generated successfully',
                'count' => count($allRecommendations)
            ]);
        } catch (\Exception $e) {
            Log::error('Generate recommendations error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error generating recommendations'], 500);
        }
    }
    
    public function getAllRecommendations()
    {
        try {
            $recommendations = DB::table('recommendations')
                ->join('student_info', 'recommendations.student_number', '=', 'student_info.student_number')
                ->join('job_postings', 'recommendations.job_id', '=', 'job_postings.job_id')
                ->orderBy('recommendations.generated_at', 'desc')
                ->orderBy('recommendations.rank_position')
                ->select(
                    'recommendations.*',
                    'student_info.first_name',
                    'student_info.last_name',
                    'student_info.program',
                    'job_postings.title',
                    'job_postings.employer_name'
                )
                ->get();
            
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('Get all recommendations error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching recommendations'], 500);
        }
    }
    
    public function markAsSent($recommendationId)
    {
        try {
            DB::table('recommendations')
                ->where('recommendation_id', $recommendationId)
                ->update([
                    'status' => 'sent',
                    'updated_at' => now()
                ]);
            
            return response()->json(['success' => true, 'message' => 'Recommendation marked as sent']);
        } catch (\Exception $e) {
            Log::error('Mark as sent error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }
}