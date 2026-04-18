<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class StudentInfo extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'student_info';
    protected $primaryKey = 'student_number';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'middle_name',
        'cvsu_email',
        'password',
        'program',
        'course',
        'year_level',
        'general_weighted_average',
        'contact_number',
        'section',
        'town',
        'profile_photo',
        'shs_school',
        'shs_year_grad',
        'shs_type',
        'hs_school',
        'hs_year_grad',
        'hs_type',
        'elem_school',
        'elem_year_grad',
        'elem_type',
        // New personal detail fields
        'birth_date',
        'birth_place',
        'full_address',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function skills()
    {
        return $this->hasMany(StudentSkill::class, 'student_id', 'student_number');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'student_number', 'student_number');
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthIdentifierName()
    {
        return 'student_number';
    }
}