<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    use HasFactory;

    protected $table = 'student_skills';
    protected $primaryKey = 'student_skill_id';
    
    protected $fillable = [
        'student_id',
        'skill_name',
        'proficiency_level',
        'years_experience'
    ];

    public function student()
    {
        return $this->belongsTo(StudentInfo::class, 'student_id', 'student_number');
    }
}