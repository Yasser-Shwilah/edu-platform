<?php
// app/Http/Controllers/SubscriptionController.php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function subscribe($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        // إذا لم يشترك من قبل
        if (! $user->courses()->where('course_id', $courseId)->exists()) {
            $user->courses()->attach($courseId, [
                'status' => 'active',
                'subscribed_at' => now(),
            ]);
        }

        return redirect()
            ->route('courses.show', $courseId)
            ->with('success', 'تم الاشتراك في الكورس بنجاح!');
    }

    public function unsubscribe($courseId)
    {
        $user = Auth::user();
        $user->courses()->detach($courseId);

        return redirect()
            ->route('courses.show', $courseId)
            ->with('success', 'تم إلغاء الاشتراك.');
    }
}
