<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Traits\ResponseTrait;

class AnnouncementController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'is_important' => 'required|boolean',
            'course_id' => 'required|exists:courses,id',
        ]);

        $announcement = Announcement::create([
            'content' => $request->content,
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_important' => $request->is_important,
            'course_id' => $request->course_id,
            'created_by' => auth('admin')->id(),
        ]);
        return $this->successResponse('تم انشاء الأعلان بنجاح.', [
            'announcement' => $announcement,
        ]);
    }
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

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return $this->successResponse('تم حذف الإعلان بنجاح.');
    }

    public function index()
    {
        $announcements = Announcement::whereDate('expiry_date', '>=', now())
            ->orderByDesc('is_important')
            ->orderByDesc('publish_date')
            ->get();

        return $this->successResponse('قائمة الإعلانات', [
            'announcements' => $announcements,
        ]);
    }
}
