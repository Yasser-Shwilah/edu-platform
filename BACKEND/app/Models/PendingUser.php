<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',         // student أو instructor
        'name',
        'email',
        'password',
        'otp_code',
        'expires_at',
    ];
}
