<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'content',
        'publish_date',
        'expiry_date',
        'is_important',
        'course_id',
        'created_by_id',
        'created_by_type',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function createdBy()
    {
        return $this->morphTo();
    }
}
