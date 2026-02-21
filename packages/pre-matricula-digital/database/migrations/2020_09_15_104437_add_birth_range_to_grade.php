<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthRangeToGrade extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'grades';

    protected $viewFile = __DIR__ . '/../sqls/views/grades-2020-09-15.sql';

    public function createTable()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('start_age');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->date('start_birth')->nullable();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('end_age');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->date('end_birth')->nullable();
        });
    }

    public function dropTable()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->integer('start_age')->nullable();
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->integer('end_age')->nullable();
        });
    }
}
