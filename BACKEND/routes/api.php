<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\InstructorAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// routes/api.php



// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Route::post('login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Route::middleware(['auth:sanctum', 'instructor'])->group(function () {});

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-code', [PasswordResetController::class, 'verifyResetCode']);



Route::prefix('student')->group(function () {
    Route::post('register', [StudentAuthController::class, 'register']);
    Route::post('verify-otp', [StudentAuthController::class, 'verifyOtp']);
    Route::post('login', [StudentAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [StudentAuthController::class, 'logout']);
});

Route::prefix('instructor')->group(function () {
    Route::post('register', [InstructorAuthController::class, 'register']);
    Route::post('verify-otp', [InstructorAuthController::class, 'verifyOtp']);
    Route::post('login', [InstructorAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [InstructorAuthController::class, 'logout']);
});
