<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('process_period', function (Blueprint $table) {
            $table->index(['process_id']);
            $table->index(['period_id']);
        });
    }

    public function down()
    {
        Schema::table('process_period', function (Blueprint $table) {
            $table->dropIndex(['process_id']);
            $table->dropIndex(['period_id']);
        });
    }
};
