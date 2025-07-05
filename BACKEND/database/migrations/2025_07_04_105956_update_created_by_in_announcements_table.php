<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }

            $table->unsignedBigInteger('created_by_id')->after('course_id');
            $table->string('created_by_type')->after('created_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['created_by_id', 'created_by_type']);

            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
        });
    }
};
