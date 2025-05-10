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
        Schema::table('lectures', function (Blueprint $table) {
            $table->string('url')->after('content');
            $table->unsignedBigInteger('size')->after('url');
            $table->string('file_type')->after('size');
            $table->timestamp('upload_date')->nullable()->after('file_type');
            $table->unsignedInteger('download_count')->default(0)->after('upload_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lectures', function (Blueprint $table) {
            //
        });
    }
};
