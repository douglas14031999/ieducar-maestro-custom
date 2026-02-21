<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->boolean('auto_reject_by_days')->default(false);
            $table->smallInteger('auto_reject_days')->nullable();
        });

        Schema::table('preregistrations', function (Blueprint $table) {
            $table->timestamp('summoned_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('preregistrations', function (Blueprint $table) {
            $table->dropColumn('summoned_at');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('auto_reject_by_days');
            $table->dropColumn('auto_reject_days');
        });
    }
};
