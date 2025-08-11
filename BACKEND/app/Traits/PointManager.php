<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Http\Controllers\LearningTrackController;

trait PointManager
{
    /**
     * زيادة نقاط المستخدم وتحديث الشارات بناءً على النقاط والمشاريع المنجزة.
     *
     * @param int $userId
     * @param int $points عدد النقاط التي تضاف
     * @return void
     */
    public function addPointsAndUpdateBadges(int $userId, int $points): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $user->increment('points', $points);

        $controller = new LearningTrackController();
        $controller->assignBadgeBasedOnPoints($userId);
    }
    public function addTrackPoints($userId, $trackId, $points)
    {
        DB::table('learning_track_user')
            ->where('user_id', $userId)
            ->where('learning_track_id', $trackId)
            ->increment('track_points', $points);
    }
}
