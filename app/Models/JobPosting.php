<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $table = 'job_postings';
    protected $primaryKey = 'job_id';
    
    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'job_type',
        'industry',
        'location',
        'salary_range_min',
        'salary_range_max',
        'min_gwa',
        'min_year_level',
        'is_ojt',
        'posted_date',
        'deadline_date',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'views_count',
        'applications_count'
    ];

    protected $casts = [
        'posted_date' => 'date',
        'deadline_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_ojt' => 'boolean',
        'salary_range_min' => 'decimal:2',
        'salary_range_max' => 'decimal:2',
        'min_gwa' => 'decimal:2'
    ];

    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }

    public function requiredSkills()
    {
        return $this->hasMany(RequiredSkill::class, 'job_id', 'job_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id', 'job_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by', 'admin_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(Admin::class, 'rejected_by', 'admin_id');
    }
}