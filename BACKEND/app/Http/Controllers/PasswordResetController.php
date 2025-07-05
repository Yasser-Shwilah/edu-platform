<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'البريد غير موجود'], 404);
        }

        $token = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'expires_at' => now()->addMinutes(15),
                'created_at' => now()
            ]
        );

        Log::info("رمز التحقق للبريد {$user->email} هو: $token");

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

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'الحساب غير موجود'], 404);
        }

        $token = $user->createToken($user->role . '-token')->plainTextToken;

        return response()->json([
            'message' => 'تم التحقق من الرمز وتسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
