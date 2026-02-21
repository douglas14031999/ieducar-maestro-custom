<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolYearTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.school_years';

    protected $viewFile = __DIR__ . '/../sqls/views/school_years.sql';

    public function createTable()
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('year');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('school_years');
    }
}
