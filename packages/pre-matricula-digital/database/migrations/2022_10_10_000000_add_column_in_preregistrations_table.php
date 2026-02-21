<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->bigInteger('in_classroom_id')->nullable();
            $table->index(['in_classroom_id']);
        });
    }

    public function down()
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->dropColumn('in_classroom_id');
        });
    }
};
