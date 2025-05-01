<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class LectureController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $lectures = Lecture::with('course')->get();
        return $this->successResponse('تم جلب المحاضرات بنجاح', $lectures);
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
            'content' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $lecture = Lecture::create($validated);

        return $this->successResponse('تم إنشاء المحاضرة بنجاح', $lecture, 201);
    }

    public function update(Request $request, $id)
    {
        $lecture = Lecture::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

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
