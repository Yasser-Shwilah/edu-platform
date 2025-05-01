<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\InstructorAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExamController;


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


// Password Reset Routes
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-code', [PasswordResetController::class, 'verifyResetCode']);

// Student Routes

// Authentication Routes
Route::prefix('student')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('student')->group(function () {


        // Student-specific Routes
        Route::prefix('courses')->group(function () {
            Route::get('/', [CourseController::class, 'index']);
            Route::get('/{id}', [CourseController::class, 'show']);
        });

        Route::prefix('lectures')->group(function () {
            Route::get('/course/{courseId}', [LectureController::class, 'courseLectures']);
        });

        Route::prefix('comments')->group(function () {
            Route::get('/post/{postId}', [CommentController::class, 'postComments']);
            Route::post('/', [CommentController::class, 'store']);
            Route::put('/{id}', [CommentController::class, 'update']);
            Route::delete('/{id}', [CommentController::class, 'destroy']);
        });

        Route::prefix('exams')->group(function () {
            Route::get('/course/{courseId}', [ExamController::class, 'courseExams']);
            Route::get('/corrected', [ExamController::class, 'correctedExams']);
            Route::get('/uncorrected', [ExamController::class, 'uncorrectedExams']);
        });
    });
});

// Instructor Auth Routes (بدون توثيق)
Route::prefix('instructor')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

// Instructor Protected Routes (تحتاج توثيق)
Route::prefix('instructor')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
    });

    Route::prefix('lectures')->group(function () {
        Route::get('/course/{courseId}', [LectureController::class, 'courseLectures']);
        Route::post('/', [LectureController::class, 'store']);
        Route::put('/{id}', [LectureController::class, 'update']);
        Route::delete('/{id}', [LectureController::class, 'destroy']);
    });

    Route::prefix('comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::get('/post/{postId}', [CommentController::class, 'postComments']);
        Route::post('/', [CommentController::class, 'store']);
        Route::put('/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/course/{courseId}', [ExamController::class, 'courseExams']);
        Route::post('/', [ExamController::class, 'store']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::patch('/{id}/correct', [ExamController::class, 'correctExam']);
    });
});
