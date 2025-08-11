<?php

namespace App\Http\Controllers;

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

    // إنشاء دورة تدريبية
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:training_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'lessons' => 'required|array|min:1',
            'lessons.*.title' => 'required|string|max:255',
            'lessons.*.video_url' => 'nullable|string',
            'lessons.*.video_file' => 'nullable|file|mimes:mp4,avi,mov',
            'lessons.*.duration' => 'required|integer|min:1',
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

            $imagePath = null;
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('training_courses', 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }

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
                'is_free' => true,
            ]);

            foreach ($request->lessons as $index => $lesson) {
                $videoUrl = $lesson['video_url'] ?? null;

                if (isset($lesson['video_file']) && $lesson['video_file'] instanceof \Illuminate\Http\UploadedFile) {
                    $videoPath = $lesson['video_file']->store('training_videos', 'public');
                    $videoUrl = asset('storage/' . $videoPath);
                }

                if ($videoUrl && !preg_match('/^https?:\/\//', $videoUrl)) {
                    $videoUrl = asset(ltrim($videoUrl, '/'));
                }

                TrainingLesson::create([
                    'training_course_id' => $course->id,
                    'title' => $lesson['title'],
                    'video_url' => $videoUrl,
                    'duration' => $lesson['duration'],
                ]);
            }

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

            $course->image_url = $imageUrl;

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

    public function index()
    {


        $courses = TrainingCourse::with('category')->latest()->paginate(10);
        return $this->successResponse('قائمة الدورات التدريبية', $courses);
    }

    public function show($id)
    {


        $course = TrainingCourse::with(['category', 'lessons', 'examQuestions'])->findOrFail($id);
        return $this->successResponse('تفاصيل الدورة التدريبية', $course);
    }

    public function update(Request $request, $id)
    {


        $course = TrainingCourse::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:training_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'certificate_type' => 'nullable|in:attendance,official',
            'is_free' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('training_courses', 'public');
                $course->image = $imagePath;
            }

            $course->update($request->only([
                'title',
                'description',
                'category_id',
                'certificate_type',
                'is_free',
            ]));

            DB::commit();
            return $this->successResponse('تم تحديث الدورة بنجاح', $course);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ أثناء التحديث', [$e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {


        $course = TrainingCourse::findOrFail($id);
        $course->delete();

        return $this->successResponse('تم حذف الدورة بنجاح');
    }
}
