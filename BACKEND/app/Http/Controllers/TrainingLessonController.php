<?php

namespace App\Http\Controllers;

use App\Models\TrainingLesson;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

namespace App\Http\Controllers;

use App\Models\TrainingProgress;
use App\Models\TrainingLesson;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\TrainingEnrollment;

class TrainingLessonController extends Controller
{
    use ResponseTrait;

    public function index($courseId)
    {
        $lessons = TrainingLesson::where('training_course_id', $courseId)
            ->orderBy('created_at')
            ->get();

        return $this->successResponse('دروس الدورة', $lessons);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_course_id' => 'required|exists:training_courses,id',
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'duration' => 'required|integer|min:1',
        ]);

        $lesson = TrainingLesson::create($validated);

        return $this->successResponse('تم إنشاء الدرس بنجاح', $lesson);
    }

    public function update(Request $request, $id)
    {
        $lesson = TrainingLesson::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'video_url' => 'sometimes|url',
            'duration' => 'sometimes|integer|min:1',
        ]);

        $lesson->update($validated);

        return $this->successResponse('تم تعديل الدرس بنجاح', $lesson);
    }

    public function destroy($id)
    {
        $lesson = TrainingLesson::findOrFail($id);
        $lesson->delete();

        return $this->successResponse('تم حذف الدرس بنجاح');
    }
    public function viewLesson($lessonId)
    {
        $user = auth()->user();
        $lesson = TrainingLesson::findOrFail($lessonId);

        $enrolled = TrainingEnrollment::where('user_id', $user->id)
            ->where('training_course_id', $lesson->training_course_id)
            ->exists();

        if (!$enrolled) {
            return $this->errorResponse('يجب الاشتراك في الدورة أولاً', 403);
        }

        $previousLesson = TrainingLesson::where('training_course_id', $lesson->training_course_id)
            ->where('id', '<', $lesson->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousLesson) {
            $progress = TrainingProgress::where('user_id', $user->id)
                ->where('training_lesson_id', $previousLesson->id)
                ->first();

            if (!$progress || $progress->progress_percentage < 70) {
                return $this->errorResponse('يجب إنجاز 70% من الدرس السابق أولاً', 403);
            }
        }

        return $this->successResponse('يمكنك الآن مشاهدة الدرس', $lesson);
    }
    public function updateLessonProgress(Request $request, $lessonId)
    {
        $user = auth()->user();
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);

        $progress = TrainingProgress::updateOrCreate(
            ['user_id' => $user->id, 'training_lesson_id' => $lessonId],
            ['progress_percentage' => $request->progress]
        );

        return $this->successResponse('تم تحديث التقدم', $progress);
    }
}
