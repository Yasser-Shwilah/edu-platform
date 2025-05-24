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
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // $table->string('slug')->unique();
            $table->text('description');
            $table->foreignId('category_id')->constrained('training_categories')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->float('rating')->default(0);
            $table->integer('lessons_count')->default(0);
            $table->integer('enrollment_count')->default(0);
            $table->enum('certificate_type', ['attendance', 'official']);
            $table->boolean('is_certified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_courses');
    }
};
