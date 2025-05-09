<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = Student::where('email', $request->email)->first();
        $instructor = Instructor::where('email', $request->email)->first();

        if (!$student && !$instructor) {
            return response()->json(['message' => 'البريد غير موجود'], 404);
        }

        $userType = $student ? 'student' : 'instructor';
        $userEmail = $student ? $student->email : $instructor->email;

        $token = rand(100000, 999999);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $userEmail],
            ['token' => $token, 'expires_at' => now()->addMinutes(15), 'created_at' => now()]
        );

        Log::info("رمز التحقق للبريد {$request->email} هو: $token");

        return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.']);
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset || now()->greaterThan($reset->expires_at)) {
            return response()->json(['message' => 'رمز غير صالح أو منتهي الصلاحية'], 400);
        }

        $student = Student::where('email', $request->email)->first();
        $instructor = Instructor::where('email', $request->email)->first();

        if (!$student && !$instructor) {
            return response()->json(['message' => 'الحساب غير موجود'], 404);
        }

        $user = $student ? $student : $instructor;
        $authToken = $user->createToken($user instanceof Student ? 'student-token' : 'instructor-token')->plainTextToken;

        return response()->json([
            'message' => 'تم التحقق من الرمز وتسجيل الدخول بنجاح',
            'token' => $authToken,
            'user' => $user,
        ]);
    }
}
