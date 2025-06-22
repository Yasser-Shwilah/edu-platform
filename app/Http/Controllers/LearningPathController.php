<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningPath;

class LearningPathController extends Controller
{
    public function index()
    {
        $paths = LearningPath::all();

        return response()->json([
            'message' => 'جميع المسارات التعليمية',
            'data' => $paths
        ]);
    }

    public function show($id)
    {
        $path = LearningPath::with('courses')->find($id);

        if (!$path) {
            return response()->json([
                'message' => 'المسار غير موجود'
            ], 404);
        }

        return response()->json([
            'message' => 'كورسات المسار التعليمي',
            'path' => $path->name,
            'courses' => $path->courses
        ]);
    }

    


}
