<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\TrainingCertificate;
use App\Models\TrainingProgress;
use App\Models\TrainingCourse;

class TrainingCertificateController extends Controller
{
    use ResponseTrait;

    public function userCertificates($userId)
    {
        $certificates = TrainingCertificate::with('trainingCourse')
            ->where('user_id', $userId)
            ->get();

        return $this->successResponse('شهادات المستخدم', $certificates);
    }
    public function checkAndIssueCertificate($courseId)
    {
        $user = auth()->user();
        $course = TrainingCourse::with('lessons')->findOrFail($courseId);

        if ($course->certificate_type === 'official') {
            return $this->errorResponse('هذه الدورة تتطلب اجتياز امتحان للحصول على الشهادة', 403);
        }

        foreach ($course->lessons as $lesson) {
            $progress = TrainingProgress::where('user_id', $user->id)
                ->where('training_lesson_id', $lesson->id)
                ->value('progress_percentage');

            if ($progress < 100) {
                return $this->errorResponse('لم تُنجز جميع الدروس بنسبة كافية بعد', 403);
            }
        }

        $existing = TrainingCertificate::where('user_id', $user->id)
            ->where('training_course_id', $courseId)
            ->first();

        if ($existing && $existing->type === 'official') {
            return $this->errorResponse('لقد حصلت بالفعل على شهادة معتمدة لهذه الدورة', 400);
        }

        $certificate = TrainingCertificate::firstOrCreate([
            'user_id' => $user->id,
            'training_course_id' => $courseId,
        ], [
            'type' => 'attendance',
            'issued_at' => now(),
        ]);

        return $this->successResponse('تم إصدار شهادة الحضور بنجاح', $certificate);
    }
}
