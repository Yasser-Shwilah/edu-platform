<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// submissions table
return new class extends Migration
{
    public function up(): void
    {

        // ✅ هذا الصحيح
Schema::create('exams', function (Blueprint $table) {
    $table->id(); // مهم جداً أن يكون id من نوع BIGINT UNSIGNED
    $table->string('title');
    $table->timestamps();
});

     /*  
        Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('assignment_id')->nullable()->constrained('assignments')->onDelete('cascade');
      //  $table->foreignId('exam_id')->nullable()->constrained('exams')->onDelete('cascade');
        $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->float('grade')->nullable();
        $table->timestamps();
        });  */

    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
