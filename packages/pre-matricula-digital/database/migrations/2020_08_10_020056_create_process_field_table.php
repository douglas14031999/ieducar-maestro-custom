<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('process_id');
            $table->integer('field_id');
            $table->integer('order');
            $table->boolean('required')->default(false);
            $table->integer('weight')->default(0);
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
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
        Schema::dropIfExists('process_fields');
    }
}
