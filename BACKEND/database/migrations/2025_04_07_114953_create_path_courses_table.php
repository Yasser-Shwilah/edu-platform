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
        Schema::create('path_courses', function (Blueprint $table) {
            $table->id('path_course_id');
            $table->unsignedBigInteger('path_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamps();

            $table->foreign('path_id')->references('path_id')->on('learning_paths')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_courses');
    }
};
