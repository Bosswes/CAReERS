<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredSkill extends Model
{
    use HasFactory;

    protected $table = 'required_skills';
    protected $primaryKey = 'required_skill_id';
    
    protected $fillable = [
        'job_id',
        'skill_name',
        'importance_level'
    ];

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }
}