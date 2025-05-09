<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Course;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function booted()
    {
        static::created(function ($enrollment) {
            Course::where('id', $enrollment->course_id)->increment('enrollment_count');
        });

        static::deleted(function ($enrollment) {
            Course::where('id', $enrollment->course_id)->decrement('enrollment_count');
        });
    }
}
