<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;

class ExamController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $exams = Exam::with('course')->get();
        return $this->successResponse('تم جلب الامتحانات بنجاح', $exams);
    }

    public function courseExams($courseId)
    {
        $exams = Exam::where('course_id', $courseId)->get();
        return $this->successResponse('تم جلب امتحانات الكورس بنجاح', $exams);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array',
            'course_id' => 'required|exists:courses,id',
        ]);

        $existingExam = Exam::where('course_id', $request->course_id)->first();
        if ($existingExam) {
            $existingExam->delete();
        }

        $exam = Exam::create([
            'title' => $request->title,
            'questions' => json_encode($request->questions),
            'course_id' => $request->course_id,
        ]);

        return $this->successResponse('تم إنشاء الامتحان بنجاح', $exam, 201);
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'questions' => 'sometimes|array',
        ]);

        if (isset($validated['questions'])) {
            $validated['questions'] = json_encode($validated['questions']);
        }

        $exam->update($validated);

        return $this->successResponse('تم تحديث الامتحان بنجاح', $exam);
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return $this->successResponse('تم حذف الامتحان بنجاح');
    }

    public function correctExam(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $exam->update([
            'is_corrected' => true,
        ]);

        return $this->successResponse('تم تصحيح الامتحان بنجاح', $exam);
    }

    public function correctedExams()
    {
        $correctedExams = Exam::where('is_corrected', true)->get();
        return $this->successResponse('تم جلب الامتحانات المصححة بنجاح', $correctedExams);
    }

    public function uncorrectedExams()
    {
        $uncorrectedExams = Exam::where('is_corrected', false)->get();
        return $this->successResponse('تم جلب الامتحانات غير المصححة بنجاح', $uncorrectedExams);
    }
}
