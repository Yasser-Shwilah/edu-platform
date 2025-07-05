<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    protected $fillable = ['user_id', 'course_id', 'video_id'];
}
