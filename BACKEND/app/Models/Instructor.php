<?php
// app/Models/Instructor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Instructor extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'instructor_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
    ];

    protected $hidden = [
        'password',
    ];
}
