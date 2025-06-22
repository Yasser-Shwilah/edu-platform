<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $announcements = Announcement::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        })->paginate(10);

        return view('announcements.index', compact('announcements', 'search'));
    }
}
