<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->enum('vote_type', ['like', 'dislike'])->default('like');
            $table->timestamps();

            $table->unique(['user_id', 'blog_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
