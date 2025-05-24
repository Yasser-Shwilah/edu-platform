<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCertificate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'training_course_id', 'type', 'certificate_file'];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'training_course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
