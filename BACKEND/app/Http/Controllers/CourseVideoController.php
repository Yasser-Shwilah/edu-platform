<?php


namespace App\Http\Controllers;

use App\Models\CourseVideo;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Storage;

class CourseVideoController extends Controller
{
    use ResponseTrait;
    public function show($id)
    {
        $video = CourseVideo::findOrFail($id);

        $userId = auth()->id();
        $isEnrolled = \App\Models\Enrollment::where('user_id', $userId)
            ->where('course_id', $video->course_id)
            ->exists();

        if (!$isEnrolled) {
            return $this->errorResponse('غير مصرح لك بمشاهدة هذا الفيديو، يجب أن تكون مسجلاً في الكورس', 403);
        }

        $video->video_url = asset('storage/' . $video->video_url);

        return $this->successResponse('تم جلب الفيديو بنجاح', $video);
    }


    public function index($courseId)
    {
        $videos = CourseVideo::where('course_id', $courseId)->get();

        $videos->transform(function ($video) {
            $video->video_url = asset('storage/' . $video->video_url);
            return $video;
        });

        return $this->successResponse('تم جلب فيديوهات الكورس بنجاح', $videos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,avi,mkv,3gp|max:512000',
            'course_id' => 'required|exists:courses,id',
        ]);

        $videoPath = $request->file('video')->store('course_videos', 'public');
        $extension = $request->file('video')->getClientOriginalExtension();

        $video = CourseVideo::create([
            'title' => $validated['title'],
            'type' => $extension,
            'video_url' => $videoPath,
            'course_id' => $validated['course_id'],
        ]);

        return $this->successResponse('تم رفع الفيديو بنجاح', $video, 201);
    }

    public function update(Request $request, $id)
    {
        $video = CourseVideo::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'video' => 'sometimes|file|mimes:mp4,avi,mkv|max:204800',
        ]);

        if ($request->hasFile('video')) {
            if ($video->video_url) {
                Storage::disk('public')->delete($video->video_url);
            }

            $newPath = $request->file('video')->store('course_videos', 'public');
            $validated['video_url'] = $newPath;
            $validated['type'] = $request->file('video')->getClientOriginalExtension();
        }

        $video->update($validated);

        return $this->successResponse('تم تحديث الفيديو بنجاح', $video);
    }

    public function destroy($id)
    {
        $video = CourseVideo::findOrFail($id);
        if ($video->video_url) {
            Storage::disk('public')->delete($video->video_url);
        }
        $video->delete();

        return $this->successResponse('تم حذف الفيديو بنجاح');
    }
}
