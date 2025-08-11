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
        Schema::create('learning_track_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_track_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->float('progress_percentage')->default(0);
            $table->integer('weeks_remaining')->default(0);
            $table->integer('points')->default(0); // للنقاط
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_track_progress');
    }
};
