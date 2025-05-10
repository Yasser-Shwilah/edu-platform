<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'content',
        'url',
        'size',
        'file_type',
        'upload_date',
        'download_count',
        'course_id',
    ];


    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
