<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class CourseController extends Controller
{
    use ResponseTrait;

    public function index(Request $request, $pathId)
    {
        $query = Course::with('instructor')
            ->whereHas('learningPaths', function ($q) use ($pathId) {
                $q->where('learning_paths.id', $pathId);
            });
    
        // فلترة حسب اسم الكورس (بحث جزئي)
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
    
        // فلترة حسب السنة الدراسية
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
    
        // فلترة حسب اسم الدكتور
        if ($request->filled('instructor_name')) {
            $query->whereHas('instructor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->instructor_name . '%');
            });
        }
    
        $courses = $query->get();
    
        return $this->successResponse('تم جلب الكورسات بنجاح', $courses);
    }
    

    public function show($id)
    {
        $course = Course::with('instructor','exams')->findOrFail($id);

        $lectures = $course->lectures->groupBy('type');

        $course->lectures_grouped = $lectures;
        unset($course['lectures']);
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

    //student

    public function st_index()
    {
        $courses = Course::with('student')->get();
        return $this->successResponse('تم جلب الكورسات بنجاح', $courses);
    }

    public function st_show($id)
    {
        $course = Course::with('student', 'lectures')->findOrFail($id);
        return $this->successResponse('تم جلب تفاصيل الكورس بنجاح', $course);
    }

}
