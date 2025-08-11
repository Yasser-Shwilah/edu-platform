<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningTrackProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'track_id',
        'points',
        'learning_track_id',
    ];

    public function track()
    {
        return $this->belongsTo(LearningTrack::class, 'learning_track_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('completed_at')
            ->withTimestamps();
    }
}
