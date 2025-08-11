<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'required_points'];

    public function awardedBadges()
    {
        return $this->hasMany(LearningTrackBadge::class);
    }
}
