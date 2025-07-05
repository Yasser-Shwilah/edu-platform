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
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'course_id')) {
                $table->foreignId('course_id')->nullable()->change();
            }

            if (Schema::hasColumn('announcements', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            if (!Schema::hasColumn('announcements', 'created_by_id')) {
                $table->unsignedBigInteger('created_by_id')->after('course_id');
            }

            if (!Schema::hasColumn('announcements', 'created_by_type')) {
                $table->string('created_by_type')->after('created_by_id');
            }

            $table->index(['created_by_id', 'created_by_type']);
        });
    }
};
