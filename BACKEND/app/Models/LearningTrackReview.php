<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningTrackReview extends Model
{
    protected $fillable = ['learning_track_id', 'user_id', 'rating', 'comment'];

    public function track()
    {
        return $this->belongsTo(LearningTrack::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
