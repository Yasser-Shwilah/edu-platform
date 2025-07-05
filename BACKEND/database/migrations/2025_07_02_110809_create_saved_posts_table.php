<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedPostsTable extends Migration
{
    public function up()
    {
        Schema::create('saved_posts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['user_id', 'blog_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_posts');
    }
}
