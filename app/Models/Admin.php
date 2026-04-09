<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'username',
        'password',
        'prefix',
        'suffix',
        'admin_email',
        'can_manage_job',
        'can_manage_user',
        'can_manage_announcement',
        'can_view_reports',
        'admin_id',
        'department',
        'position',
        'office_location',
        'internal_phone',
        'admin_level',
        'can_manage_users',
        'can_manage_jobs',
        'can_manage_announcements',
        'last_activity'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'can_manage_job' => 'boolean',
        'can_manage_user' => 'boolean',
        'can_manage_announcement' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_manage_users' => 'boolean',
        'can_manage_jobs' => 'boolean',
        'can_manage_announcements' => 'boolean',
        'last_activity' => 'datetime'
    ];

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name . ' ' . $this->suffix);
    }
}