<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.courses';

    protected $viewFile = __DIR__ . '/../sqls/views/courses-2020-08-11.sql';

    public function createTable()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('courses');
    }
}
