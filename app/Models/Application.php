<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'application_id';
    
    protected $fillable = [
        'student_number',
        'job_id',
        'employer_id',
        'status',
        'employer_notes',
        'application_date',
        'reviewed_at'
    ];

    protected $casts = [
        'application_date' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(StudentInfo::class, 'student_number', 'student_number');
    }

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }
}