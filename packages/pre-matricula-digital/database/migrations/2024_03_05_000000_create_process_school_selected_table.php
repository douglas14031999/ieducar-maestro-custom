<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('process_school_selected', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->references('id')->on('processes');
            $table->foreignId('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('process_school_selected');
    }
};
