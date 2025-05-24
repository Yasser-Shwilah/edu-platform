<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingExamResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'training_course_id', 'score', 'passed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }
}
