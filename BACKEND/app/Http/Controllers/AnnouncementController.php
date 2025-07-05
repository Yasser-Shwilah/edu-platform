<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Traits\ResponseTrait;

class AnnouncementController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Announcement::with('createdBy', 'course')
            ->whereDate('expiry_date', '>=', now());

        if ($request->filled('created_by_type')) {
            $type = $request->created_by_type;

            $map = [
                'admin' => \App\Models\Admin::class,
                'instructor' => \App\Models\User::class,
            ];

            if (isset($map[$type])) {
                $query->where('created_by_type', $map[$type]);
            }
        }

        $announcements = $query
            ->orderByDesc('is_important')
            ->orderByDesc('publish_date')
            ->get();

        $result = $announcements->map(function ($announcement) {
            return [
                'id' => $announcement->id,
                'content' => $announcement->content,
                'publish_date' => $announcement->publish_date->format('Y-m-d'),
                'expiry_date' => $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d') : null,
                'is_important' => $announcement->is_important,
                'course' => $announcement->course ? [
                    'id' => $announcement->course->id,
                    'title' => $announcement->course->title,
                ] : null,

                'created_by' => $announcement->createdBy ? [
                    'id' => $announcement->createdBy->id,
                    'name' => $announcement->createdBy->name ?? 'غير معروف',
                    'type' => class_basename($announcement->created_by_type),
                ] : null,
            ];
        });

        return $this->successResponse('قائمة الإعلانات', ['announcements' => $result]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'is_important' => 'required|boolean',
            'course_id' => 'nullable|exists:courses,id',
        ]);


        $user = auth()->user();

        if (!$user || !in_array(get_class($user), [\App\Models\Admin::class, \App\Models\User::class])) {
            return $this->errorResponse('غير مصرح لك برفع الإعلان', 401);
        }


        $announcement = Announcement::create([
            'content' => $request->content,
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_important' => $request->is_important,
            'course_id' => $request->course_id,
            'created_by_id' => $user->id,
            'created_by_type' => get_class($user),
        ]);


        return $this->successResponse('تم انشاء الإعلان بنجاح.', [
            'announcement' => $announcement,
        ]);
    }

    // تحديث إعلان موجود
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'content' => 'string|nullable',
            'publish_date' => 'date|nullable',
            'expiry_date' => 'date|nullable|after_or_equal:publish_date',
            'is_important' => 'boolean|nullable',
            'course_id' => 'exists:courses,id|nullable',
        ]);

        $announcement->update($request->only([
            'content',
            'publish_date',
            'expiry_date',
            'is_important',
            'course_id',
        ]));

        return $this->successResponse('تم تعديل الإعلان بنجاح.', [
            'announcement' => $announcement,
        ]);
    }

    // حذف إعلان
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return $this->successResponse('تم حذف الإعلان بنجاح.');
    }
}
