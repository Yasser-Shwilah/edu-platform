<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'name',
        'email',
        'password',
        'otp_code',
        'expires_at',
        'title',
        'bio',
        'avatar_url',
        'phone',
        'department',
        'academic_year',
        'specialization',
    ];
}
