<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
    use ResponseTrait;
    public function view($id)
    {
        $lecture = Lecture::findOrFail($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($lecture->content)) {
            return $this->errorResponse('الملف غير موجود', 404);
        }

        $filePath = $disk->path($lecture->content);
        $mimeType = $disk->mimeType($lecture->content);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }


    public function download($id)
    {
        $lecture = Lecture::findOrFail($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($lecture->content)) {
            return $this->errorResponse('الملف غير موجود', 404);
        }

        return $disk->download($lecture->content);
    }
    public function courseLectures($courseId)
    {
        $lectures = Lecture::where('course_id', $courseId)->get();
        return $this->successResponse('تم جلب محاضرات الكورس بنجاح', $lectures);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mkv,zip',
            'course_id' => 'required|exists:courses,id',
        ]);

        $filePath = $request->file('content')->store('lectures', 'public');

        $lecture = Lecture::create([
            'title' => $validated['title'],
            'content' => $filePath,
            'course_id' => $validated['course_id'],
        ]);

        return $this->successResponse('تم إنشاء المحاضرة بنجاح', $lecture, 201);
    }

    public function update(Request $request, $id)
    {
        $lecture = Lecture::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mkv,zip',
        ]);

        if ($request->hasFile('content')) {
            $filePath = $request->file('content')->store('lectures', 'public');
            $validated['content'] = $filePath;
        }

        $lecture->update($validated);

        return $this->successResponse('تم تحديث المحاضرة بنجاح', $lecture);
    }

    public function destroy($id)
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->delete();

        return $this->successResponse('تم حذف المحاضرة بنجاح');
    }
}
