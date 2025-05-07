<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['student_id', 'exam_id', 'answers'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}

