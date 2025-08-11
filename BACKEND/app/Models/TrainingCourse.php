<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'description',
        'image',
        'rating',
        'lessons_count',
        'enrollment_count',
        'certificate_type',
        'is_certified',
        'is_free',
        'last_updated',
    ];

    public function category()
    {
        return $this->belongsTo(TrainingCategory::class);
    }

    public function lessons()
    {
        return $this->hasMany(TrainingLesson::class);
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function progress()
    {
        return $this->hasMany(TrainingProgress::class);
    }

    public function exam()
    {
        return $this->hasOne(TrainingExam::class);
    }

    public function examQuestions()
    {
        return $this->hasMany(TrainingExamQuestion::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot('completed_at')
            ->withTimestamps();
    }
}
