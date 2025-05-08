<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'description',
        'category',
        'price',
        'instructor_id',
        'year',
        'specialization',
        'lessons_count',
        'last_updated',
        'is_free',
        'thumbnail_url',
        'rating',
        'enrollment_count',
    ];


    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class, 'course_id', 'course_id');
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function videoLectures()
    {
        return $this->hasMany(CourseLecture::class, 'course_id', 'id')->where('type', 'video');
    }
}
