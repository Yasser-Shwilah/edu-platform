<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
    ];

    public function post()
    {
        return $this->belongsTo(Blog::class, 'post_id', 'blog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
