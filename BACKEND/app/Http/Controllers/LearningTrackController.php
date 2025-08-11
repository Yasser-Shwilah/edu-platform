<?php

namespace App\Http\Controllers;

use App\Models\LearningTrack;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LearningTrackProject;
use Illuminate\Support\Facades\DB;
use App\Traits\PointManager;
use App\Traits\ResponseTrait;

class LearningTrackController extends Controller
{
    use PointManager, ResponseTrait;

    public function enrollStudent($trackId)
    {
        $track = LearningTrack::findOrFail($trackId);
        $user = auth()->user();

        if ($track->students()->where('user_id', $user->id)->exists()) {
            return $this->errorResponse('أنت مشترك بالفعل في هذا المسار', [], 409);
        }

        $track->students()->attach($user->id);

        return $this->successResponse('تم الاشتراك في المسار بنجاح');
    }

    public function index(Request $request)
    {
        $type = $request->query('type');
        $user = auth()->user();

        $tracks = LearningTrack::with('instructor', 'trainingCourses', 'projects')
            ->when($type && $type !== 'الكل', fn($q) => $q->where('type', $type))
            ->get()
            ->map(function ($track) use ($user) {
                $track->is_enrolled = $track->students()->where('user_id', $user->id)->exists();
                return $track;
            });

        return $this->successResponse('قائمة المسارات', $tracks);
    }

    public function studentTrackStats($trackId)
    {
        $track = LearningTrack::with('trainingCourses', 'projects', 'instructor')->findOrFail($trackId);
        $user = auth()->user();

        $completedCourses = $user->completedCourses()
            ->whereIn('course_id', $track->trainingCourses->pluck('id'))->count();

        $completedProjects = $user->completedProjects()
            ->whereIn('project_id', $track->projects->pluck('id'))->count();

        $progress = $track->trainingCourses->count() > 0
            ? ($completedCourses / $track->trainingCourses->count()) * 100
            : 0;

        $weeksRemaining = now()->diffInWeeks($track->start_date, false);

        $durationWeeks = $track->credit_hours ? ceil($track->credit_hours / 10) : null;

        $trackPoints = DB::table('learning_track_user')
            ->where('user_id', $user->id)
            ->where('learning_track_id', $trackId)
            ->value('track_points') ?? 0;

        return $this->successResponse('إحصائيات المسار', [
            'progress_percentage' => round($progress, 1),
            'completed_courses' => $completedCourses,
            'completed_projects' => $completedProjects,
            'credit_hours' => $track->credit_hours,
            'duration_weeks' => $durationWeeks,
            'level' => $track->level,
            'instructor' => $track->instructor->name,
            'department' => $track->instructor->department,
            'start_date' => $track->start_date,
            'prerequisites' => $track->prerequisites,
            'weeks_remaining' => $weeksRemaining,
            'badges' => $user->badges()->pluck('name'),
            'track_points' => $trackPoints,
        ]);
    }

    public function leaderboard($trackId)
    {
        $leaderboard = DB::table('learning_track_user as ltu')
            ->join('users', 'users.id', '=', 'ltu.user_id')
            ->where('ltu.learning_track_id', $trackId)
            ->select('users.id', 'users.name', 'ltu.track_points as points')
            ->orderByDesc('ltu.track_points')
            ->limit(10)
            ->get();

        $leaderboard = $leaderboard->map(function ($user, $index) {
            return [
                'rank' => $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'points' => $user->points,
            ];
        });

        return $this->successResponse('لوحة الصدارة', $leaderboard);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'level' => 'required|in:مبتدئ,متوسط,متقدم',
            'type' => 'required|in:أساسي,تخصصي,مهني',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
            'start_date' => 'nullable|date',
            'credit_hours' => 'nullable|integer|min:0',
            'prerequisites' => 'nullable|string',
            'training_course_ids' => 'nullable|array',
            'training_course_ids.*' => 'exists:training_courses,id',
        ]);

        $track = LearningTrack::create([
            'title' => $validated['title'],
            'level' => $validated['level'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'credit_hours' => $validated['credit_hours'] ?? null,
            'prerequisites' => $validated['prerequisites'] ?? null,
            'instructor_id' => $request->user()->id,
        ]);

        if (!empty($validated['training_course_ids'])) {
            $track->trainingCourses()->sync($validated['training_course_ids']);
        }

        return $this->successResponse('تم إنشاء المسار بنجاح', $track->load('trainingCourses'), 201);
    }

    public function show($id)
    {
        $track = LearningTrack::with('trainingCourses', 'projects', 'instructor')->findOrFail($id);
        return $this->successResponse('تفاصيل المسار', $track);
    }

    public function update(Request $request, $id)
    {
        $track = LearningTrack::findOrFail($id);

        $track->update($request->only([
            'title',
            'level',
            'type',
            'description',
            'image',
            'start_date',
            'credit_hours',
            'prerequisites'
        ]));

        return $this->successResponse('تم تحديث المسار بنجاح', $track);
    }

    public function destroy($id)
    {
        $track = LearningTrack::findOrFail($id);
        $track->delete();

        return $this->successResponse('تم حذف المسار بنجاح');
    }

    public function grantProgressPoints($trackId)
    {
        $user = auth()->user();
        $track = LearningTrack::with('trainingCourses')->findOrFail($trackId);

        $totalCourses = $track->trainingCourses->count();
        $completedCourses = $user->completedCourses()
            ->whereIn('course_id', $track->trainingCourses->pluck('id'))
            ->count();

        $progress = ($totalCourses > 0) ? ($completedCourses / $totalCourses) * 100 : 0;

        $points = 0;
        if ($progress >= 100) $points = 100;
        elseif ($progress >= 75) $points = 75;
        elseif ($progress >= 50) $points = 50;

        if ($points > 0) {
            $this->addTrackPoints($user->id, $trackId, $points);
            $this->assignBadgeBasedOnPoints($user->id);
        }

        return $this->successResponse('تم احتساب النقاط بناءً على التقدم', [
            'points_added' => $points,
            'progress' => round($progress, 1)
        ]);
    }


    public function assignBadgeBasedOnPoints($userId)
    {
        $user = User::findOrFail($userId);

        $totalTrackPoints = DB::table('learning_track_user')
            ->where('user_id', $userId)
            ->sum('track_points');

        $pointBadges = [
            ['name' => 'مبتدئ', 'min_points' => 50],
            ['name' => 'متقدم', 'min_points' => 150],
            ['name' => 'محترف', 'min_points' => 300],
        ];

        foreach ($pointBadges as $badgeData) {
            $badge = Badge::firstOrCreate(['name' => $badgeData['name']]);
            if ($totalTrackPoints >= $badgeData['min_points']) {
                $user->badges()->syncWithoutDetaching([$badge->id]);
            }
        }

        if ($user->completedProjects()->count() >= 1) {
            $badge = Badge::firstOrCreate(['name' => 'مشروع أول']);
            $user->badges()->syncWithoutDetaching([$badge->id]);
        }

        $learningTracks = LearningTrack::with(['trainingCourses', 'projects'])->get();

        foreach ($learningTracks as $track) {
            $courseIds = $track->trainingCourses->pluck('id');
            $projectIds = $track->projects->pluck('id');

            $completedCourses = $user->completedCourses()->whereIn('course_id', $courseIds)->count();
            $completedProjects = $user->completedProjects()->whereIn('project_id', $projectIds)->count();

            if ($track->trainingCourses->count() && $completedCourses === $track->trainingCourses->count()) {
                $user->badges()->syncWithoutDetaching([Badge::firstOrCreate(['name' => 'اكمال المسار'])->id]);
            }

            if (
                $track->trainingCourses->count() && $track->projects->count() &&
                $completedCourses === $track->trainingCourses->count() &&
                $completedProjects === $track->projects->count()
            ) {
                $user->badges()->syncWithoutDetaching([Badge::firstOrCreate(['name' => 'متميز'])->id]);
            }
        }

        $firstCourse = $user->completedCourses()->orderBy('pivot_created_at')->first();
        if ($firstCourse && $user->created_at->diffInDays($firstCourse->pivot->created_at) <= 3) {
            $user->badges()->syncWithoutDetaching([Badge::firstOrCreate(['name' => 'متعلم سريع'])->id]);
        }

        $topUserIds = DB::table('learning_track_user')
            ->select('user_id', DB::raw('SUM(track_points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->limit(10)
            ->pluck('user_id');

        if ($topUserIds->contains($user->id)) {
            $user->badges()->syncWithoutDetaching([Badge::firstOrCreate(['name' => 'متسابق'])->id]);
        }

        return $this->successResponse('الشارات الحالية', $user->badges()->pluck('name'));
    }

    public function availableBadges()
    {
        return $this->successResponse('قائمة الشارات', Badge::all());
    }

    public function reviewTrack(Request $request, $trackId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $track = LearningTrack::findOrFail($trackId);

        $track->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $track->update(['rating' => $track->reviews()->avg('rating')]);

        return $this->successResponse('تمت إضافة التقييم بنجاح', ['average_rating' => round($track->rating, 2)]);
    }

    public function markProjectAsDone(Request $request, $projectId)
    {
        $user = auth()->user();
        $project = LearningTrackProject::findOrFail($projectId);

        if ($user->completedProjects()->where('project_id', $project->id)->exists()) {
            return $this->errorResponse('تم تنفيذ المشروع مسبقاً', [], 409);
        }

        DB::transaction(function () use ($user, $project) {
            $user->completedProjects()->attach($project->id, ['completed_at' => now()]);
            $this->addTrackPoints($user->id, $project->learning_track_id, $project->points);
            $this->assignBadgeBasedOnPoints($user->id);
        });

        return $this->successResponse('تم تنفيذ المشروع بنجاح وتمت إضافة النقاط');
    }


    public function getStudentProjects($trackId)
    {
        $user = auth()->user();
        $track = LearningTrack::with('projects')->findOrFail($trackId);

        $projects = $track->projects->map(function ($project) use ($user) {
            $project->is_completed = $user->completedProjects->contains($project->id);
            return $project;
        });

        return $this->successResponse('مشاريع الطالب ضمن المسار', $projects);
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'learning_track_id' => 'required|exists:learning_tracks,id',
        ]);

        $project = LearningTrackProject::create($request->only(['title', 'description', 'points', 'learning_track_id']));

        return $this->successResponse('تم رفع المشروع بنجاح', $project);
    }
}
