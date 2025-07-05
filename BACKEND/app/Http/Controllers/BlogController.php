<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Vote;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class BlogController extends Controller
{
    use ResponseTrait;

    // قائمة التدوينات
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Blog::with(['author', 'tags'])->withCount('comments');

        if ($type) {
            $query->where('type', $type);
        }

        $blogs = $query->latest()->paginate(10);

        return $this->successResponse('قائمة التدوينات', [
            'posts' => $blogs
        ]);
    }

    // إنشاء تدوينة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:article,discussion',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'codeSnippet' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
        }

        $post = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'image' => $path,
            'codeSnippet' => $request->codeSnippet,
            'user_id' => $request->user()->id,
        ]);

        // ربط الوسوم
        if ($request->filled('tags')) {
            $tagsIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tag->increment('usage_count');
                $tagsIds[] = $tag->id;
            }
            $post->tags()->sync($tagsIds);
        }

        return $this->successResponse('تم إنشاء المنشور', [
            'post' => $post->load('tags')
        ]);
    }

    // عرض تدوينة مفردة
    public function show($id)
    {
        $post = Blog::with(['author', 'tags', 'comments.user'])->findOrFail($id);
        return $this->successResponse('تفاصيل المنشور', [
            'post' => $post
        ]);
    }

    // تعديل التدوينة
    public function update(Request $request, $id)
    {
        $post = Blog::findOrFail($id);
        if ($post->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بالتعديل', [], 403);
        }

        $data = $request->only(['title', 'content', 'codeSnippet']);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = $path;
        }

        $post->update($data);

        if ($request->filled('tags')) {
            $tagsIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagsIds[] = $tag->id;
            }
            $post->tags()->sync($tagsIds);
        }

        return $this->successResponse('تم التحديث بنجاح', [
            'post' => $post->load('tags')
        ]);
    }

    // حذف التدوينة
    public function destroy($id)
    {
        $post = Blog::findOrFail($id);
        if ($post->user_id !== request()->user()->id) {
            return $this->errorResponse('غير مصرح لك بالحذف', [], 403);
        }

        $post->delete();

        return $this->successResponse('تم حذف المنشور');
    }

    // التصويت على منشور
    public function vote(Request $request, $id)
    {
        $request->validate([
            'vote_type' => 'required|in:like,dislike',
        ]);

        $post = Blog::findOrFail($id);

        Vote::updateOrCreate(
            ['user_id' => $request->user()->id, 'blog_id' => $id],
            ['vote_type' => $request->vote_type]
        );

        return $this->successResponse('تم التصويت', ['post' => $post]);
    }

    // حفظ / إلغاء حفظ منشور
    public function toggleSave(Request $request, $id)
    {
        $post = Blog::findOrFail($id);

        $saved = $request->user()->savedPosts()->toggle($id);

        $message = count($saved['attached']) > 0 ? 'تم الحفظ' : 'تم إلغاء الحفظ';

        return $this->successResponse($message, ['post' => $post]);
    }

    // عرض المنشورات المحفوظة
    public function savedPosts(Request $request)
    {
        $posts = $request->user()->savedPosts()->with('author', 'tags')->paginate(10);

        return $this->successResponse('المنشورات المحفوظة', ['posts' => $posts]);
    }

    // البحث في المنشورات
    public function search(Request $request)
    {
        $q = $request->query('q');
        $tags = $request->query('tags') ? explode(',', $request->query('tags')) : [];

        $query = Blog::with(['author', 'tags']);

        if ($q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%$q%")
                    ->orWhere('content', 'like', "%$q%")
                    ->orWhere('codeSnippet', 'like', "%$q%");
            });
        }

        if (!empty($tags)) {
            $query->whereHas('tags', function ($q3) use ($tags) {
                $q3->whereIn('name', $tags);
            });
        }

        $posts = $query->latest()->paginate(10);

        return $this->successResponse('نتائج البحث', ['posts' => $posts]);
    }
}
