<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // علاقة: المسار يحتوي على عدة كورسات
   
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'path_courses');
    }
}