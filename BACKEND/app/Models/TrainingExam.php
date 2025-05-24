<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingExam extends Model
{
    use HasFactory;

    protected $fillable = ['training_course_id', 'questions'];

    protected $casts = [
        'questions' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }
}
