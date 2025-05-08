<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\PendingUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;

class AdminAuthController extends Controller
{
    use ResponseTrait;

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email|unique:pending_users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        PendingUser::create([
            'type'       => 'admin',
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'otp_code'   => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::raw("رمز التحقق الخاص بك هو: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('رمز التحقق من البريد الإلكتروني (أدمن)');
        });

        return $this->successResponse('تم إرسال رمز التحقق إلى بريدك الإلكتروني.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp_code' => 'required|string',
        ]);

        $pending = PendingUser::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('type', 'admin')
            ->where('expires_at', '>', now())
            ->first();

        if (!$pending) {
            return $this->errorResponse('رمز التحقق غير صحيح أو منتهي الصلاحية.', [], 422);
        }

        $admin = Admin::create([
            'name'     => $pending->name,
            'email'    => $pending->email,
            'password' => $pending->password,
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;
        $pending->delete();

        return $this->successResponse('تم إنشاء حساب الأدمن بنجاح.', [
            'token' => $token,
            'user'  => $admin,
            'type'  => 'admin',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->errorResponse('البيانات المدخلة غير صحيحة.', [], 422);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return $this->successResponse('تم تسجيل الدخول بنجاح.', [
            'token' => $token,
            'user'  => $admin,
            'type'  => 'admin',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse('تم تسجيل الخروج بنجاح.');
    }
}
