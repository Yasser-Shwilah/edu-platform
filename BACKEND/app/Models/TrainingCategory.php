<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/TrainingCategory.php
class TrainingCategory extends Model
{
    protected $fillable = ['name'];

    public function trainingCourses()
    {
        return $this->hasMany(TrainingCourse::class);
    }
}
