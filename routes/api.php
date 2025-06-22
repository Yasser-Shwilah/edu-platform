<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CourseSubscriptionController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\ExamSubmissionController;
use App\Http\Controllers\AnnouncementController; // ✅ تأكد من إضافة هذا

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Password Reset Routes
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-code', [PasswordResetController::class, 'verifyResetCode']);

// Student Routes
Route::prefix('student')->group(function () {

    // Authentication Routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('student')->group(function () {

        // Learning Paths
        Route::prefix('learning-paths')->group(function () {
            Route::get('/', [LearningPathController::class, 'index']);
            Route::get('/{id}/courses', [CourseController::class, 'index']);
            Route::get('/courses/{courseId}', [CourseController::class, 'show']);
        });

        // Exams
        Route::prefix('exams')->group(function () {
            Route::get('/courses/{courseId}/exams', [ExamController::class, 'getExamsForStudent']);
            Route::get('/exams/{examId}/download', [ExamController::class, 'downloadExamFile']);
            Route::post('/submit-exam', [ExamSubmissionController::class, 'store'])->name('submit.exam');
            Route::get('/', [ExamController::class, 'index']);
            Route::get('/course/{courseId}', [ExamController::class, 'courseExams']);
        });

        // Lectures
        Route::prefix('lectures')->group(function () {
            Route::get('/course/{courseId}', [LectureController::class, 'courseLectures']);
            Route::get('/{id}/view', [LectureController::class, 'view']);
            Route::get('/{id}/download', [LectureController::class, 'download']);
        });

        // Comments
        Route::prefix('comments')->group(function () {
            Route::get('/post/{postId}', [CommentController::class, 'postComments']);
            Route::post('/', [CommentController::class, 'store']);
            Route::put('/{id}', [CommentController::class, 'update']);
            Route::delete('/{id}', [CommentController::class, 'destroy']);
        });

        // Course Subscriptions
        Route::prefix('courses')->group(function () {
            Route::post('/{id}/subscribe', [CourseSubscriptionController::class, 'subscribe']);
            Route::delete('/{id}/unsubscribe', [CourseSubscriptionController::class, 'unsubscribe']);
        });

        // Announcements
        Route::prefix('announcements')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index'])->name('announcements.index');
        });
    });
});

// Instructor Auth Routes
Route::prefix('instructor')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

// Instructor Protected Routes
Route::prefix('instructor')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Courses
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
        Route::post('/{courseId}/subscribe', [CourseSubscriptionController::class, 'subscribeToCourse']); // ✅ تصحيح الرابط
    });

    // Lectures
    Route::prefix('lectures')->group(function () {
        Route::get('/course/{courseId}', [LectureController::class, 'courseLectures']);
        Route::post('/', [LectureController::class, 'store']);
        Route::put('/{id}', [LectureController::class, 'update']);
        Route::delete('/{id}', [LectureController::class, 'destroy']);
    });

    // Comments
    Route::prefix('comments')->group(function () {
        Route::get('/post/{postId}', [CommentController::class, 'postComments']);
        Route::post('/', [CommentController::class, 'store']);
        Route::put('/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    // Exams
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::get('/course/{courseId}', [ExamController::class, 'courseExams']);
        Route::post('/', [ExamController::class, 'store']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::patch('/{id}/correct', [ExamController::class, 'correctExam']);
    });
});
