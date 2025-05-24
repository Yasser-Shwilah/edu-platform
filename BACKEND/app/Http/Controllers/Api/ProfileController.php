<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // عرض بيانات البروفايل
    public function show($id)
    {
        $user = User::with([
            'enrollments.course',
            'trainingEnrollments.course',
            'trainingCertificates.course',
        ])->findOrFail($id);

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'profile_image' => $user->profile_image
                ? asset('storage/' . $user->profile_image)
                : asset('profile_images/default-avatar.png'),
            'university_courses' => $user->enrollments->map(function ($enrollment) {
                return [
                    'title' => $enrollment->course->title,
                    'status' => $enrollment->status,
                ];
            }),
            'training_courses' => $user->trainingEnrollments->map(function ($enrollment) {
                return [
                    'title' => $enrollment->course->title,
                ];
            }),
            'certificates' => $user->trainingCertificates->map(function ($cert) {
                return [
                    'course_title' => $cert->course->title,
                    'certificate_file' => $cert->certificate_file,
                    'type' => $cert->type,
                ];
            }),
        ]);
    }

    // رفع صورة البروفايل
    public function uploadProfileImage(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // حذف الصورة السابقة إن وجدت
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // رفع الصورة الجديدة
        $path = $request->file('image')->store('profile_images', 'public');

        $user->update(['profile_image' => $path]);

        return response()->json([
            'message' => 'تم رفع صورة البروفايل بنجاح',
            'image_url' => asset('storage/' . $path),
        ]);
    }
}
