<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessStageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_stages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('process_id');
            $table->integer('process_stage_type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('observation')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('radius')->nullable();
            $table->boolean('renewal_at_same_school')->default(false);
            $table->boolean('allow_waiting_list')->default(false);
            $table->timestamps();

            $table->foreign('process_id')
                ->references('id')
                ->on('processes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_stages');
    }
}
