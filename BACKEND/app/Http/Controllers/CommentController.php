<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class CommentController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        $comments = Comment::with('post', 'user')->get();
        return $this->successResponse('تم جلب التعليقات بنجاح', $comments);
    }

    public function postComments($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user')->get();
        return $this->successResponse('تم جلب تعليقات البوست بنجاح', $comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:blogs,id',
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'post_id' => $validated['post_id'],
            'user_id' => auth()->id(),
        ]);

        return $this->successResponse('تم إنشاء التعليق بنجاح', $comment, 201);
    }


    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'sometimes|string',
        ]);

        $comment->update($validated);

        return $this->successResponse('تم تحديث التعليق بنجاح', $comment);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return $this->successResponse('تم حذف التعليق بنجاح');
    }
}
