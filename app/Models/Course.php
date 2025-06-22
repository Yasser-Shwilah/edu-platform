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
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class, 'path_courses','path_id','id');
    }
    
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

     public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('subscribed_at', 'status')
                    ->withTimestamps();
    }
}
