<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Mail;

class StudentAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:pending_users|unique:students',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        PendingUser::create([
            'type' => 'student',
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::raw("Your verification code is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Email Verification Code');
        });

        return response()->json([
            'message' => 'Verification code sent to your email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string'
        ]);

        $pending = PendingUser::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'student')
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return response()->json(['message' => 'Invalid or expired OTP code'], 422);
        }

        $user = Student::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'academic_year' => null,
            'specialization' => null,
        ]);

        $pending->delete();

        $token = $user->createToken('student-token')->plainTextToken;

        return response()->json([
            'message' => 'Account verified and created successfully.',
            'token' => $token,
            'user' => $user,
            'type' => 'student'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = Student::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('student-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'type' => 'student'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
