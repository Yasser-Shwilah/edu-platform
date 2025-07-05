<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class TagController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $tags = Tag::orderBy('usage_count', 'desc')->get();
        return $this->successResponse('قائمة الوسوم الشائعة', ['tags' => $tags]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:tags,name']);

        $tag = Tag::create(['name' => $request->name]);

        return $this->successResponse('تم إنشاء الوسم', ['tag' => $tag]);
    }
}
