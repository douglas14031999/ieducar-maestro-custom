<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('process_grade', function (Blueprint $table) {
            $table->index(['process_id']);
            $table->index(['grade_id']);
        });
    }

    public function down()
    {
        Schema::table('process_grade', function (Blueprint $table) {
            $table->dropIndex(['process_id']);
            $table->dropIndex(['grade_id']);
        });
    }
};
