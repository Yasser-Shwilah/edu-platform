<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyContentColumnInLecturesTable extends Migration
{
    public function up()
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->string('content')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->text('content')->change();
        });
    }
}
