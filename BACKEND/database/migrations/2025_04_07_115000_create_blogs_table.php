<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id('post_id');
            $table->string('title');
            $table->text('content');
            $table->unsignedBigInteger('author_id');
            $table->enum('author_type', ['student', 'instructor']); // نوع المستخدم
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('author_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
