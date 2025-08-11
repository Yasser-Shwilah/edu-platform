<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'publish_date',
        'expiry_date',
        'is_important',
        'created_by_id',
        'created_by_type',
    ];



    protected $casts = [
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];



    public function createdBy()
    {
        return $this->morphTo();
    }
}
