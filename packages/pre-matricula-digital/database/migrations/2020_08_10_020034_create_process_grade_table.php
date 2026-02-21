<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessGradeTable extends Migration
{
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_grade', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('process_id');
            $table->integer('grade_id');

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
        });

        if ($this->useThirdPartyDatabase()) {
            return;
        }

        Schema::table('process_grade', function (Blueprint $table) {
            $table->foreign('grade_id')->references('id')->on('grades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_grade');
    }
}
