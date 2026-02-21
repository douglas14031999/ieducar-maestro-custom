<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->index(['preregistration_type_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->dropIndex(['preregistration_type_id']);
            $table->dropIndex(['status']);
        });
    }
};
