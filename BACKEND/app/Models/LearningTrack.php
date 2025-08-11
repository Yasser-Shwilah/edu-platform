<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTrack extends Model
{
    protected $fillable = [
        'title',
        'level',
        'type',
        'description',
        'image',
        'instructor_id',
        'start_date',
        'credit_hours',
        'prerequisites',
        'rating',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function trainingCourses()
    {
        return $this->belongsToMany(TrainingCourse::class, 'learning_track_courses');
    }


    public function projects()
    {
        return $this->hasMany(LearningTrackProject::class);
    }

    public function progresses()
    {
        return $this->hasMany(LearningTrackProgress::class);
    }
    public function reviews()
    {
        return $this->hasMany(LearningTrackReview::class);
    }
    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('track_points')
            ->withTimestamps();
    }
}
