<?php

namespace App\Http\Controllers;
use App\Models\Submission;

use Illuminate\Http\Request;

class ExamSubmissionController extends Controller
{
  public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
        ]);

        $path = $request->file('file')->store('submissions', 'public');

        Submission::create([
            'user_id' => auth()->id(),
            'exam_id' => $request->exam_id,
            'file_path' => $path,
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح!');
    }
}
