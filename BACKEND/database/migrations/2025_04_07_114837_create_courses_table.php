<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// courses table
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->enum('year',['سنة أولى','سنة ثانية','سنة ثالثة','سنة رابعة','سنة خامسة']);
            $table->boolean('is_paid')->default(false);
            $table->decimal('price', 8, 2)->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
