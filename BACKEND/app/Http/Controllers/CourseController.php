<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class CourseController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $courses = Course::with('instructor')->get();
        return $this->successResponse('تم جلب الكورسات بنجاح', $courses);
    }

    public function show($id)
    {
        $course = Course::with('instructor', 'lectures')->findOrFail($id);
        return $this->successResponse('تم جلب تفاصيل الكورس بنجاح', $course);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string|max:100',
            'instructor_id' => 'required|exists:users,id',
        ]);

        $course = Course::create($validated);

        return $this->successResponse('تم إنشاء الكورس بنجاح', $course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'category' => 'sometimes|string|max:100',
        ]);

        $course->update($validated);

        return $this->successResponse('تم تحديث الكورس بنجاح', $course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return $this->successResponse('تم حذف الكورس بنجاح');
    }
}
