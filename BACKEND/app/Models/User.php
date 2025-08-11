<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'academic_year',
        'specialization',
        'title',
        'bio',
        'avatar_url',
        'phone',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function isInstructor()
    {
        return $this->role === 'instructor';
    }


    public function isStudent()
    {
        return $this->role === 'student';
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id', 'id');
    }
    public function trainingEnrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function trainingCertificates()
    {
        return $this->hasMany(TrainingCertificate::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'id');
    }
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function savedPosts()
    {
        return $this->belongsToMany(Blog::class, 'saved_posts');
    }
    public function learningTrackProgress()
    {
        return $this->hasMany(LearningTrackProgress::class);
    }

    public function learningTracks()
    {
        return $this->belongsToMany(LearningTrack::class)
            ->withPivot('track_points')
            ->withTimestamps();
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function completedCourses()
    {
        return $this->belongsToMany(TrainingCourse::class, 'course_user', 'user_id', 'course_id')
            ->withPivot('completed_at')
            ->withTimestamps();
    }


    public function completedProjects()
    {
        return $this->belongsToMany(LearningTrackProject::class, 'project_user', 'user_id', 'project_id')
            ->withPivot('completed_at')
            ->withTimestamps();
    }
    // LearningTrack.php
    public function reviews()
    {
        return $this->hasMany(LearningTrackReview::class);
    }
    public function enrolledTracks()
    {
        return $this->belongsToMany(LearningTrack::class, 'learning_track_user')->withTimestamps();
    }
}
