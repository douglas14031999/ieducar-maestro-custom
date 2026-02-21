<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.periods';

    protected $viewFile = __DIR__ . '/../sqls/views/periods.sql';

    public function createTable()
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('periods');
    }
}
