<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Blog;

class CommentController extends Controller
{
    use ResponseTrait;


    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = Blog::findOrFail($postId);

        if ($post->type !== 'discussion') {
            return $this->errorResponse('لا يمكن إضافة تعليق على هذا النوع من التدوينات', [], 403);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'post_id' => $postId,
            'user_id' => $request->user()->id,
            'user_type' => $request->user()->role,
        ]);

        return $this->successResponse('تم إضافة التعليق', [
            'comment' => $comment
        ]);
    }


    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بالتعديل', [], 403);
        }

        $comment->update([
            'content' => $request->content
        ]);

        return $this->successResponse('تم تحديث التعليق', [
            'comment' => $comment
        ]);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return $this->successResponse('تم حذف التعليق');
    }
}
