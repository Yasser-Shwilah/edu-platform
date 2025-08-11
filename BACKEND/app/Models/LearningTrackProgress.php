<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTrackProgress extends Model
{
    protected $fillable = ['learning_track_id', 'user_id', 'progress_percentage', 'weeks_remaining', 'points'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(LearningTrack::class, 'learning_track_id');
    }

    public function badges()
    {
        return $this->hasMany(LearningTrackBadge::class);
    }
}
