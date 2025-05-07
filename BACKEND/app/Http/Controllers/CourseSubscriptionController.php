<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseSubscriptionController extends Controller
{
    use Illuminate\Support\Facades\Auth;

    public function __construct()
        {
            $this->middleware('auth:api'); // استخدام الـ token للتحقق من المستخدم
        }

        public function subscribeToCourse(Request $request, $courseId)
        {
            // جلب ID الطالب من الـ token
            $studentId = Auth::id(); 
    
            // جلب الكورس باستخدام الـ ID
            $course = Course::findOrFail($courseId);
    
            // سعر الكورس
            $coursePrice = $course->price;
    
            // إنشاء اشتراك في الكورس
            $subscription = Subscription::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'price' => $coursePrice,
            ]);
    
            /*/ توليد PDF بعد الاشتراك
            $pdf = PDF::loadView('pdf.subscription', [
                'student' => Auth::user(), 
                'course' => $course,
                'price' => $coursePrice,
                'subscription_date' => now()
            ]);  
    
            // إرجاع ملف PDF للمستخدم
            return $pdf->download('subscription_'.$studentId.'_'.$courseId.'.pdf'); /*/
        }
}
