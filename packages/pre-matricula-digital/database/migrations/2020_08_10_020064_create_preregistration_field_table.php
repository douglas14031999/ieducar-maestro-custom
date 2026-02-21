<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreregistrationFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preregistration_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('preregistration_id');
            $table->integer('field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('preregistration_id')->references('id')->on('preregistrations');
            $table->foreign('field_id')->references('id')->on('fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preregistration_fields');
    }
}
