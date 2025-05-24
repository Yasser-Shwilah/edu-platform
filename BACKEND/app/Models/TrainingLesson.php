<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingLesson extends Model
{
    protected $fillable = ['training_course_id', 'title', 'video_url', 'duration'];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }
    public function progresses()
    {
        return $this->hasMany(TrainingProgress::class, 'lesson_id');
    }
}
