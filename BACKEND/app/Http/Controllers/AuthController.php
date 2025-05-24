<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\PendingUser;
use App\Models\User;

class AuthController extends Controller
{
    use ResponseTrait;

    public function register(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,instructor',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pending_users|unique:students,email|unique:instructors,email',
            'password' => 'required|string|min:6|confirmed',
            'title' => 'required_if:type,instructor|string|nullable',
            'bio' => 'required_if:type,instructor|string|nullable',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'required_if:type,instructor|string|nullable',
            'department' => 'required_if:type,instructor|string|nullable',
        ]);

        $otp = rand(100000, 999999);

        $avatarPath = null;
        if ($request->hasFile('avatar_url')) {
            $avatarPath = $request->file('avatar_url')->store('avatars', 'public');
        }

        PendingUser::create([
            'type' => $request->type,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(15),
            'title' => $request->title,
            'bio' => $request->bio,
            'avatar_url' => $avatarPath,
            'phone' => $request->phone,
            'department' => $request->department,
        ]);

        Mail::raw("رمز التحقق الخاص بك هو: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('رمز التحقق من البريد الإلكتروني');
        });

        return $this->successResponse('تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string',
        ]);

        $pending = PendingUser::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return $this->errorResponse('رمز التحقق غير صحيح أو منتهي الصلاحية.', [], 422);
        }

        if ($pending->type == 'student') {
            $user = User::create([
                'name' => $pending->name,
                'email' => $pending->email,
                'password' => $pending->password,
                'academic_year' => null,
                'specialization' => null,
            ]);

            $token = $user->createToken('student-token')->plainTextToken;
            $userType = 'student';
        } else {
            $user = User::create([
                'name' => $pending->name,
                'email' => $pending->email,
                'password' => $pending->password,
                'title' => $pending->title,
                'bio' => $pending->bio,
                'avatar_url' => $pending->avatar_url,
                'phone' => $pending->phone,
                'department' => $pending->department,
            ]);

            $token = $user->createToken('instructor-token')->plainTextToken;
            $userType = 'instructor';
        }

        $pending->delete();

        return $this->successResponse('تم إنشاء الحساب بنجاح.', [
            'token' => $token,
            'user' => $user,
            'type' => $userType,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,instructor',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($request->type == 'student') {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('email', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('البيانات المدخلة غير صحيحة.', [], 422);
        }

        $token = $user->createToken($request->type . '-token')->plainTextToken;

        return $this->successResponse('تم تسجيل الدخول بنجاح.', [
            'token' => $token,
            'user' => $user,
            'type' => $request->type,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('تم تسجيل الخروج بنجاح.');
    }
}
