<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// path_courses table
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('path_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')->constrained('learning_paths')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('path_courses');
    }
};
