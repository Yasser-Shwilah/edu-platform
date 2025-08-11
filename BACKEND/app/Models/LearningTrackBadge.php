<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTrackBadge extends Model
{
    protected $fillable = ['learning_track_progress_id', 'badge_id', 'awarded_at'];

    public function progress()
    {
        return $this->belongsTo(LearningTrackProgress::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
