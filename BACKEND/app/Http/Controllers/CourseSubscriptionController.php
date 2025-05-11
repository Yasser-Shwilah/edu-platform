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
            
                // التحقق مما إذا كان الطالب مشتركًا بالفعل في الكورس
                $existingSubscription = Subscription::where('student_id', $studentId)
                                                    ->where('course_id', $courseId)
                                                    ->first();
            
                if ($existingSubscription) {
                    return response()->json([
                        'message' => 'أنت مشترك بالفعل في هذا الكورس.'
                    ], 400); // 400 Bad Request
                }
            
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
            
                return response()->json([
                    'message' => 'تم الاشتراك في الكورس بنجاح.',
                    'subscription' => $subscription
                ], 201); // 201 Created
            }
            
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
