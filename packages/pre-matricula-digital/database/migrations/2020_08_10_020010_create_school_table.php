<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.schools';

    protected $viewFile = __DIR__ . '/../sqls/views/schools-2020-08-11.sql';

    public function createTable()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->float('latitude');
            $table->float('longitude');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('schools');
    }
}
