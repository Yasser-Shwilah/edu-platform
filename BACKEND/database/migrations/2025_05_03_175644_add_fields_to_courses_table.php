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
            $table->enum('year', ['first', 'second', 'third', 'fourth', 'fifth'])->after('category');
            $table->enum('specialization', ['general', 'software', 'networking', 'ai'])->default('general')->after('year');
            $table->integer('lessons_count')->default(0)->after('specialization');
            $table->timestamp('last_updated')->nullable()->after('lessons_count');
            $table->boolean('is_free')->default(false)->after('last_updated');
            $table->decimal('rating', 3, 2)->default(0.0)->after('is_free');
            $table->unsignedBigInteger('enrollment_count')->default(0)->after('rating');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'year',
                'specialization',
                'lessons_count',
                'last_updated',
                'is_free',
                'rating',
                'enrollment_count'
            ]);
        });
    }
};
