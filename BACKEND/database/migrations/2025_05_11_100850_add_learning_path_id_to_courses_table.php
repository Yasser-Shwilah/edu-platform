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
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('learning_path_id')->nullable()->after('id');

            $table->foreign('learning_path_id')
                ->references('id')
                ->on('learning_paths')
                ->onDelete('set null'); // يمكن تغييره إلى cascade إذا أردت حذف الكورسات عند حذف المسار
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['learning_path_id']);
            $table->dropColumn('learning_path_id');
        });
    }

};
