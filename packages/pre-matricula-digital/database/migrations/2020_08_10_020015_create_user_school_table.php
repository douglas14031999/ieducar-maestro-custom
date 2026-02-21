<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSchoolTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.user_school';

    protected $viewFile = __DIR__ . '/../sqls/views/user_school.sql';

    public function createTable()
    {
        Schema::create('user_school', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('school_id');
        });

        if ($this->useThirdPartyDatabase()) {
            return;
        }

        Schema::table('user_school', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('school_id')->references('id')->on('schools');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('user_school');
    }
}
