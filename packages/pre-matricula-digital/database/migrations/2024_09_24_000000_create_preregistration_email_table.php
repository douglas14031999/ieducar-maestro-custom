<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preregistration_linked_by_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preregistration_id')->constrained('preregistrations');
            $table->string('email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preregistration_linked_by_email');
    }
};
