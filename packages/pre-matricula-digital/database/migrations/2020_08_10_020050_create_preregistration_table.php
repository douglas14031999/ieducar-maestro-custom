<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreregistrationTable extends Migration
{
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preregistrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('preregistration_type_id');
            $table->integer('period_id');
            $table->integer('school_id');
            $table->integer('grade_id');
            $table->integer('process_id');
            $table->integer('process_stage_id');
            $table->integer('student_id');
            $table->integer('responsible_id');
            $table->integer('relation_type_id')->default(1);
            $table->integer('parent_id')->nullable();
            $table->integer('status')->default(1);
            $table->text('observation')->nullable();
            $table->string('protocol');
            $table->string('code');
            $table->integer('priority')->default(0);
            $table->integer('external_person_id')->nullable();
            $table->integer('classroom_id')->nullable();
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes');
            $table->foreign('process_stage_id')->references('id')->on('process_stages');
            $table->foreign('responsible_id')->references('id')->on('people');
            $table->foreign('student_id')->references('id')->on('people');
        });

        if ($this->useThirdPartyDatabase()) {
            return;
        }

        Schema::table('preregistrations', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('period_id')->references('id')->on('periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preregistrations');
    }
}
