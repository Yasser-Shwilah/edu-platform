<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
//use App\Http\Controllers\StudentAuthController;
//use App\Http\Controllers\InstructorAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CourseSubscriptionController;

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
            Route::get('/learning-paths', [LearningPathController::class, 'index']);
            Route::get('/learning-paths/{id}', [LearningPathController::class, 'show']);
        });
        
        // Courses
        Route::prefix('courses')->group(function () {
            Route::get('/', [CourseController::class, 'index']);
            Route::get('/{id}', [CourseController::class, 'show']);
        });

         // Exams
         Route::prefix('exams')->group(function () {
            Route::get('/courses/{courseId}/exams', [ExamController::class, 'getExamsForStudent']);
            Route::get('/exams/{examId}/download', [ExamController::class, 'downloadExamFile']);
            Route::post('/exams/{examId}/submit', [ExamController::class, 'submitExamAnswers']);
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

        // Exams (filtered via ?corrected=true or false)
        Route::prefix('exams')->group(function () {
            Route::get('/', [ExamController::class, 'index']);
            Route::get('/course/{courseId}', [ExamController::class, 'courseExams']);
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
        Route::post('/courses/{courseId}/subscribe', [CourseSubscriptionController::class, 'subscribeToCourse']);

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
        // Route::get('/', [CommentController::class, 'index']);
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
