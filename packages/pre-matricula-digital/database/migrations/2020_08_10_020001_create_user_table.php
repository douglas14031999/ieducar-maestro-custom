<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.user';

    protected $viewFile = __DIR__ . '/../sqls/views/user.sql';

    public function createTable()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_type_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');

            $table->foreign('user_type_id')->references('id')->on('user_types');
        });
    }

    public function dropTable()
    {
        Schema::dropIfExists('user');
    }
}
