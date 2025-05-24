<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\TrainingCourseController;
use App\Http\Controllers\TrainingCertificateController;
use App\Http\Controllers\TrainingExamController;
use App\Http\Controllers\TrainingLessonController;
use App\Http\Controllers\AdminTrainingCourseController;
use App\Http\Controllers\Api\ProfileController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================
// Public Routes
// ==========================


// Password Reset
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode']);
Route::post('/verify-code', [PasswordResetController::class, 'verifyResetCode']);

// Student Auth
Route::prefix('student')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

// Instructor Auth
Route::prefix('instructor')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('login', [AuthController::class, 'login']);
});

// Admin Auth
Route::prefix('admin')->group(function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('verify-otp', [AdminAuthController::class, 'verifyOtp']);
    Route::post('login', [AdminAuthController::class, 'login']);
});

// ==========================
// Protected Routes (Common)
// ==========================
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// ==========================
// Student Protected Routes
// ==========================
Route::middleware('auth:sanctum')->prefix('student')->group(function () {

    // ------------------------------
    // Profile
    // ------------------------------
    Route::get('/profile/{id}', [ProfileController::class, 'show']);
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadProfileImage']);

    // ---------------------------
    // Training Courses
    // ---------------------------
    Route::prefix('training-courses')->group(function () {
        Route::get('/', [TrainingCourseController::class, 'index']);
        Route::get('/{id}', [TrainingCourseController::class, 'show']);
        Route::post('/enroll', [TrainingCourseController::class, 'enroll']);
        Route::get('/category/{categoryId}/more', [TrainingCourseController::class, 'loadMoreCoursesByCategory']);
    });

    // ---------------------------
    // Training Lessons
    // ---------------------------
    Route::prefix('training-lessons')->group(function () {
        Route::get('/course/{courseId}', [TrainingLessonController::class, 'index']);
        Route::get('/{lessonId}/view', [TrainingLessonController::class, 'viewLesson']);
        Route::post('/{lessonId}/progress', [TrainingLessonController::class, 'updateLessonProgress']);
    });

    // ---------------------------
    // Training Exams
    // ---------------------------
    Route::prefix('training-exams')->group(function () {
        Route::get('/{courseId}/questions', [TrainingExamController::class, 'getExamQuestions']);
        Route::post('/{courseId}/submit', [TrainingExamController::class, 'submitExam']);
    });

    // ---------------------------
    // Training Certificates
    // ---------------------------
    Route::prefix('training-certificates')->group(function () {
        Route::get('/user/{userId}', [TrainingCertificateController::class, 'userCertificates']);
        Route::post('/{courseId}/issue', [TrainingCertificateController::class, 'checkAndIssueCertificate']);
    });

    // Courses
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::post('/enroll', [CourseController::class, 'enroll']);
        Route::post('/rate', [CourseController::class, 'rate']);
    });

    // Lectures
    Route::prefix('lectures')->group(function () {
        Route::get('/course/{courseId}', [LectureController::class, 'courseLectures']);
        Route::get('/{id}/view', [LectureController::class, 'view']);
        Route::get('/{id}/download', [LectureController::class, 'download']);
    });

    //Videos
    Route::get('/course/{courseId}', [CourseVideoController::class, 'index']);
    Route::get('/course-video/{id}', [CourseVideoController::class, 'show']);




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
    });

    // Announcements (View Only)
    Route::prefix('announcements')->group(function () {
        Route::get('/course/{courseId}', [AnnouncementController::class, 'index']);
    });
});

// ==========================
// Instructor Protected Routes
// ==========================
Route::middleware('auth:sanctum')->prefix('instructor')->group(function () {
    // Courses
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
    });
    // Videos
    Route::prefix('videos')->group(function () {
        Route::get('/course/{courseId}', [CourseVideoController::class, 'index']);
        Route::post('/', [CourseVideoController::class, 'store']);
        Route::put('/{id}', [CourseVideoController::class, 'update']);
        Route::delete('/{id}', [CourseVideoController::class, 'destroy']);
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

    // Announcements (View Only)
    Route::prefix('announcements')->group(function () {
        Route::get('/course/{courseId}', [AnnouncementController::class, 'index']);
    });
});

// ==========================
// Admin Protected Routes
// ==========================
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    // ---------------------------
    // Training Lessons Management
    // ---------------------------
    Route::prefix('training-lessons')->group(function () {
        Route::post('/', [TrainingLessonController::class, 'store']);
        Route::put('/{id}', [TrainingLessonController::class, 'update']);
        Route::delete('/{id}', [TrainingLessonController::class, 'destroy']);
    });
    // ---------------------------
    // Training Courses Management
    // ---------------------------
    Route::prefix('training-courses')->group(function () {
        Route::post('/', [AdminTrainingCourseController::class, 'store']);
        Route::post('/training-categories', [AdminTrainingCourseController::class, 'storeCategory']);

        //index/show/update/delete 
    });

    // Announcements Management (Admin Only)
    Route::prefix('announcements')->group(function () {
        Route::post('/', [AnnouncementController::class, 'store']);
        Route::put('/{id}', [AnnouncementController::class, 'update']);
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']);
    });

    // Admin Logout
    Route::post('logout', [AdminAuthController::class, 'logout']);
});
