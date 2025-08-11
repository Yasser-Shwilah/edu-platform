<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\TrainingCourse;
use App\Models\TrainingEnrollment;
use App\Models\TrainingCategory;

class TrainingCourseController extends Controller
{
    use ResponseTrait;


    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        if ($search || $categoryId) {
            $query = TrainingCourse::with('category');

            if ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            $courses = $query->withCount(['lessons', 'enrollments'])->paginate(4);

            return $this->successResponse('تم جلب الدورات حسب الفلترة', $courses);
        }

        $categories = TrainingCategory::with(['courses' => function ($q) {
            $q->withCount(['lessons', 'enrollments'])
                ->take(4);
        }])->get();

        return $this->successResponse('دورات متنوعة حسب التصنيفات', $categories);
    }

    public function show($id)
    {
        $course = TrainingCourse::with(['category', 'lessons'])->findOrFail($id);
        return $this->successResponse('تفاصيل الدورة التدريبية', $course);
    }



    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:training_courses,id',
        ]);

        $userId = auth()->id();
        $courseId = $validated['course_id'];

        $existing = TrainingEnrollment::where('user_id', $userId)
            ->where('training_course_id', $courseId)
            ->first();

        if ($existing) {
            return $this->errorResponse('أنت مشترك مسبقاً في هذه الدورة', 409);
        }

        TrainingEnrollment::create([
            'user_id' => $userId,
            'training_course_id' => $courseId,
        ]);

        $course = TrainingCourse::find($courseId);
        $course->increment('enrollment_count');

        return $this->successResponse('تم الاشتراك في الدورة بنجاح');
    }


    public function loadMoreCoursesByCategory($categoryId, Request $request)
    {
        $courses = TrainingCourse::where('category_id', $categoryId)
            ->with('category')
            ->withCount(['lessons', 'enrollments'])
            ->paginate(4);

        return $this->successResponse('المزيد من الدورات حسب التصنيف', $courses);
    }
    public function allCourses()
    {
        $courses = TrainingCourse::with('category')
            ->withCount(['lessons', 'enrollments'])
            ->latest()
            ->paginate(10);

        return $this->successResponse('جميع الدورات التدريبية', $courses);
    }
}
