<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';
    protected $primaryKey = 'announcement_id';
    
    protected $fillable = [
        'title',
        'content',
        'announcement_type',
        'target_audience',
        'start_date',
        'end_date',
        'location',
        'form_link',
        'is_published',
        'created_by',
        'views_count'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }
}