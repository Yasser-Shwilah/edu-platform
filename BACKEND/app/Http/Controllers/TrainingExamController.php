<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingCourse;
use App\Models\TrainingExamQuestion;
use App\Models\TrainingExamResult;
use App\Traits\ResponseTrait;
use App\Models\TrainingCertificate;

class TrainingExamController extends Controller
{
    use ResponseTrait;

    public function getExamQuestions($courseId)
    {
        $course = TrainingCourse::findOrFail($courseId);

        if ($course->certificate_type !== 'official') {
            return $this->errorResponse('هذه الدورة لا تحتوي على شهادة معتمدة', 400);
        }

        $questions = TrainingExamQuestion::where('training_course_id', $courseId)->get();
        return $this->successResponse('الأسئلة', $questions);
    }

    public function submitExam(Request $request, $courseId)
    {
        $userId = auth()->id();
        if (!$userId) {
            return $this->errorResponse('غير مصادق عليه - يرجى تسجيل الدخول أولاً', 401);
        }

        $course = TrainingCourse::findOrFail($courseId);
        if ($course->certificate_type !== 'official') {
            return $this->errorResponse('هذه الدورة لا تحتوي على امتحان رسمي', 400);
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        $questions = TrainingExamQuestion::where('training_course_id', $courseId)->get();
        $total = $questions->count();
        $correct = 0;

        foreach ($questions as $question) {
            if (
                isset($request->answers[$question->id]) &&
                strtoupper($request->answers[$question->id]) === $question->correct_answer
            ) {
                $correct++;
            }
        }

        $score = round(($correct / $total) * 100);
        $passed = $score >= 70;

        $result = TrainingExamResult::updateOrCreate(
            ['user_id' => $userId, 'training_course_id' => $courseId],
            ['score' => $score, 'passed' => $passed]
        );

        if ($passed) {
            TrainingCertificate::updateOrCreate([
                'user_id' => $userId,
                'training_course_id' => $courseId,
            ], [
                'type' => 'official',
                'issued_at' => now()
            ]);
        }

        return $this->successResponse(
            $passed ? 'تم اجتياز الامتحان بنجاح وتم إصدار شهادة معتمدة' : 'لم تنجح في اجتياز الامتحان',
            ['score' => $score, 'passed' => $passed]
        );
    }
}
