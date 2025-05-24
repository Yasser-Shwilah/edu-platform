<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TrainingCourse;
use App\Models\TrainingLesson;
use App\Models\TrainingCategory;
use App\Models\TrainingExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;

class AdminTrainingCourseController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:training_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'lessons' => 'required|array|min:1',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.video_url' => 'required|url',
            'certificate_type' => 'required|in:attendance,official',
            'exam_questions' => 'nullable|array',
            'exam_questions.*.question' => 'required_with:exam_questions|string',
            'exam_questions.*.option_a' => 'required_with:exam_questions|string',
            'exam_questions.*.option_b' => 'required_with:exam_questions|string',
            'exam_questions.*.option_c' => 'required_with:exam_questions|string',
            'exam_questions.*.option_d' => 'required_with:exam_questions|string',
            'exam_questions.*.correct_answer' => 'required_with:exam_questions|in:A,B,C,D',
        ]);

        try {
            DB::beginTransaction();

            // رفع الصورة إن وُجدت
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('training_courses', 'public');
            }

            // إنشاء الدورة
            $course = TrainingCourse::create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'certificate_type' => $request->certificate_type,
                'lessons_count' => count($request->lessons),
                'rating' => 0,
                'enrollment_count' => 0,
                'last_updated' => now(),
                'is_free' => true, // أو اجعلها ديناميكية لو في اشتراكات
            ]);

            // إضافة الدروس
            foreach ($request->lessons as $lesson) {
                TrainingLesson::create([
                    'training_course_id' => $course->id,
                    'title' => $lesson['title'],
                    'video_url' => $lesson['video_url'],
                    'duration' => $lesson['duration'],
                ]);
            }

            // إضافة أسئلة الامتحان إن كانت شهادة معتمدة
            if ($request->certificate_type === 'official' && $request->has('exam_questions')) {
                foreach ($request->exam_questions as $question) {
                    TrainingExamQuestion::create([
                        'training_course_id' => $course->id,
                        'question' => $question['question'],
                        'option_a' => $question['option_a'],
                        'option_b' => $question['option_b'],
                        'option_c' => $question['option_c'],
                        'option_d' => $question['option_d'],
                        'correct_answer' => strtoupper($question['correct_answer']),
                    ]);
                }
            }

            DB::commit();
            return $this->successResponse('تم إنشاء الدورة بنجاح', $course);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ أثناء إنشاء الدورة', [$e->getMessage()], 500);
        }
    }
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:training_categories,name',
        ]);

        $category = TrainingCategory::create([
            'name' => $request->name,
        ]);

        return $this->successResponse('تم إنشاء التصنيف بنجاح', $category);
    }
}
