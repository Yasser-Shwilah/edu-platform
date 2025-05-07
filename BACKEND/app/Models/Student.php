<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'academic_year',
        'specialization',
        
    ];

    protected $hidden = [
        'password',
    ];
}
