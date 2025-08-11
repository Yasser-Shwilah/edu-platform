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
        Schema::create('learning_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('level', ['مبتدئ', 'متوسط', 'متقدم']);
            $table->enum('type', ['أساسي', 'تخصصي', 'مهني']);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('instructor_id');
            $table->float('rating')->default(0);

            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_tracks');
    }
};
