<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\CourseRating;
use App\Models\CourseVideo;
use App\Models\VideoProgress;
use App\Models\User;
use App\Traits\ResponseTrait;

class CourseController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Course::with(['instructor:id,name,email,avatar_url']);

        if ($request->filled('specialization') || $request->filled('year')) {
            $query->where(function ($q) use ($request) {
                if ($request->filled('specialization')) {
                    $q->where('specialization', $request->specialization);
                }
                if ($request->filled('year')) {
                    $q->where('year', $request->year);
                }
            });
        }

        if ($request->filled('instructor_name') || $request->filled('instructor_email')) {
            $query->whereHas('instructor', function ($q) use ($request) {
                if ($request->filled('instructor_name')) {
                    $q->where('name', 'like', '%' . $request->instructor_name . '%');
                }
                if ($request->filled('instructor_email')) {
                    $q->where('email', 'like', '%' . $request->instructor_email . '%');
                }
            });
        }

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $courses = $query->get()->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'price' => $course->price,
                'is_free' => (bool) $course->is_free,
                'rating' => $course->rating,
                'year' => $course->year,
                'specialization' => $course->specialization,
                'thumbnail_url' => $course->thumbnail_url,
                'instructor' => $course->instructor ? [
                    'id' => $course->instructor->id,
                    'name' => $course->instructor->name,
                    'email' => $course->instructor->email,
                    'avatar_url' => $course->instructor->avatar_url,
                ] : null,
            ];
        });

        return $this->successResponse('تم جلب الكورسات بنجاح', $courses);
    }





    public function show($id)
    {
        $course = Course::with('instructor', 'lectures')->findOrFail($id);
        return $this->successResponse('تم جلب تفاصيل الكورس بنجاح', $course);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string|max:100',
            'year' => 'required|in:first,second,third,fourth,fifth',
            'specialization' => 'required|in:general,software,networking,ai',
            'lessons_count' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:5',
            'enrollment_count' => 'nullable|integer|min:0',
        ]);


        $validated['instructor_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail_url'] = asset('storage/' . $path);
        }

        $course = Course::create($validated);
        return $this->successResponse('تم إنشاء الكورس بنجاح', $course, 201);
    }



    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string|max:100',
            'year' => 'required|in:first,second,third,fourth,fifth',
            'specialization' => 'required|in:general,software,networking,ai',
            'lessons_count' => 'nullable|integer|min:0',
            'is_free' => 'nullable|in:true,false,1,0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'nullable|numeric|min:0|max:5',
            'enrollment_count' => 'nullable|integer|min:0',
        ]);


        $course->update($validated);
        return $this->successResponse('تم تحديث الكورس بنجاح', $course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return $this->successResponse('تم حذف الكورس بنجاح');
    }

    public function instructorProfile($id)
    {
        $instructor = User::with('courses')->findOrFail($id);

        return $this->successResponse('تم جلب معلومات المحاضر والكورسات', [
            'name' => $instructor->name,
            'email' => $instructor->email,
            'department' => $instructor->department,
            'specialization' => $instructor->specialization ?? 'غير محدد',
            'courses' => $instructor->courses
        ]);
    }

    public function studentProgress($course_id)
    {
        $userId = auth()->id();

        $total = CourseVideo::where('course_id', $course_id)->count();
        $completed = VideoProgress::where('user_id', $userId)
            ->where('course_id', $course_id)
            ->count();

        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        return $this->successResponse('تم حساب التقدم بنجاح', [
            'total_videos' => $total,
            'watched_videos' => $completed,
            'progress_percent' => $progress
        ]);
    }

    public function markVideoAsWatched(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'video_id' => 'required|exists:course_videos,id',
        ]);

        $userId = auth()->id();

        $exists = VideoProgress::where('user_id', $userId)
            ->where('course_id', $validated['course_id'])
            ->where('video_id', $validated['video_id'])
            ->exists();

        if ($exists) {
            return $this->successResponse('تم تسجيل المشاهدة مسبقاً');
        }

        VideoProgress::create([
            'user_id' => $userId,
            'course_id' => $validated['course_id'],
            'video_id' => $validated['video_id'],
        ]);

        return $this->successResponse('تم تسجيل الفيديو كمشاهد');
    }

    public function courseVideosWithProgress($course_id)
    {
        $userId = auth()->id();

        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $course_id)
            ->exists();

        if (!$isEnrolled) {
            return $this->errorResponse('غير مسموح لك بمشاهدة الفيديوهات، يجب أن تكون مسجلاً في الكورس', 403);
        }

        $videos = CourseVideo::where('course_id', $course_id)->orderBy('id')->get();

        $watched = VideoProgress::where('user_id', $userId)
            ->where('course_id', $course_id)
            ->pluck('video_id')
            ->toArray();

        $videosWithProgress = $videos->map(function ($video) use ($watched) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'video_url' => asset('storage/' . $video->video_url),
                'type' => $video->type,
                'watched' => in_array($video->id, $watched)
            ];
        });

        $total = $videos->count();
        $completed = count($watched);
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        return $this->successResponse('فيديوهات الكورس مع التقدم', [
            'videos' => $videosWithProgress,
            'total_videos' => $total,
            'watched_videos' => $completed,
            'progress_percent' => $progress
        ]);
    }
    public function rate(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $userId = auth()->id();
        $courseId = $validated['course_id'];
        $ratingValue = $validated['rating'];

        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if (!$isEnrolled) {
            return $this->errorResponse('يجب أن تكون مسجلاً في الكورس لتقييمه', 403);
        }

        $rating = \App\Models\CourseRating::updateOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
            ],
            [
                'rating' => $ratingValue,
            ]
        );

        $avgRating = \App\Models\CourseRating::where('course_id', $courseId)->avg('rating');
        Course::where('id', $courseId)->update(['rating' => round($avgRating, 1)]);

        return $this->successResponse('تم تسجيل التقييم بنجاح', [
            'course_id' => $courseId,
            'rating' => $ratingValue,
            'average_rating' => round($avgRating, 1),
        ]);
    }
    public function enroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $userId = auth()->id();
        $courseId = $validated['course_id'];

        $alreadyEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();

        if ($alreadyEnrolled) {
            return $this->errorResponse('أنت مسجل مسبقاً في هذا الكورس', 409);
        }

        Enrollment::create([
            'user_id' => $userId,
            'course_id' => $courseId,
        ]);

        $enrollmentCount = Enrollment::where('course_id', $courseId)->count();
        Course::where('id', $courseId)->update(['enrollment_count' => $enrollmentCount]);

        return $this->successResponse('تم تسجيلك في الكورس بنجاح');
    }
}
