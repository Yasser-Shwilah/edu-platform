<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\CourseRating;

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
            'year' => 'required|in:first,second,third,fourth,fifth',
            'specialization' => 'required|in:general,software,networking,ai',
            'lessons_count' => 'nullable|integer|min:0',
            'last_updated' => 'nullable|date',
            'is_free' => 'nullable|in:true,false,1,0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:5',
            'enrollment_count' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail_url'] = asset('storage/' . $path);
        }



        $course = Course::create($validated);

        return $this->successResponse('تم إنشاء الكورس بنجاح', $course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string|max:100',
            'instructor_id' => 'required|exists:users,id',
            'year' => 'required|in:first,second,third,fourth,fifth',
            'specialization' => 'required|in:general,software,networking,ai',
            'lessons_count' => 'nullable|integer|min:0',
            'last_updated' => 'nullable|date',
            'is_free' => 'nullable|in:true,false,1,0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:5',
            'enrollment_count' => 'nullable|integer|min:0',
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

    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $userId = auth()->id();
        $courseId = $validated['course_id'];

        $existing = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return $this->errorResponse('أنت مسجل بالفعل في هذا الكورس', 409);
        }

        Enrollment::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'status' => 'approved',
        ]);

        $course = Course::find($courseId);
        $course->increment('enrollment_count');

        return $this->successResponse('تم التسجيل في الكورس بنجاح');
    }

    public function rate(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $userId = auth()->id();
        $courseId = $validated['course_id'];

        CourseRating::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $courseId],
            ['rating' => $validated['rating']]
        );

        $average = CourseRating::where('course_id', $courseId)->avg('rating');
        Course::where('id', $courseId)->update(['rating' => $average]);

        return $this->successResponse('تم تسجيل تقييمك بنجاح');
    }
}
