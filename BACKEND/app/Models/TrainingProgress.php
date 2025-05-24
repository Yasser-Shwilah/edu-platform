<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgress extends Model
{
    protected $fillable = ['user_id', 'training_lesson_id', 'progress'];

    public function lesson()
    {
        return $this->belongsTo(TrainingLesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
