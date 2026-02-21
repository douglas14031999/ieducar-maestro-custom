<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.classrooms';

    protected $viewFile = __DIR__ . '/../sqls/views/classrooms-2020-08-11.sql';

    public function createTable()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('period_id');
            $table->integer('school_id');
            $table->integer('grade_id');
            $table->integer('school_year_id');
            $table->string('name');
            $table->integer('vacancy');
            $table->integer('available_vacancies');

            $table->foreign('period_id')->references('id')->on('periods');
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('school_year_id')->references('id')->on('school_years');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('classrooms');
    }
}
