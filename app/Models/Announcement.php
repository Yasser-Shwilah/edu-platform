<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public function up()
{
    Schema::create('announcements', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->decimal('price', 10, 2)->nullable(); // إن كان السعر ضروري
        $table->string('category')->nullable();
        $table->timestamps();
    });
}

}
