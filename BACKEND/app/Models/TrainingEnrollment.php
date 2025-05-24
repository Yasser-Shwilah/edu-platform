<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    protected $fillable = ['user_id', 'training_course_id'];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
