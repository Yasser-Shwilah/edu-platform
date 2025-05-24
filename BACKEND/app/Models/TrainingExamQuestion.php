<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_course_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer'
    ];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }
}
