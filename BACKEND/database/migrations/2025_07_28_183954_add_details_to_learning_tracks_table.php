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
        Schema::table('learning_tracks', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('instructor_id');
            $table->integer('credit_hours')->default(0)->after('start_date');
            $table->string('department')->nullable();
            $table->text('prerequisites')->nullable()->after('credit_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_tracks', function (Blueprint $table) {
            //
        });
    }
};
