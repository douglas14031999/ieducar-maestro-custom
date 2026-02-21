<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradeTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.grades';

    protected $viewFile = __DIR__ . '/../sqls/views/grades.sql';

    public function createTable()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('course_id');
            $table->string('name');
            $table->integer('start_age')->nullable();
            $table->integer('end_age')->nullable();
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('grades');
    }
}
