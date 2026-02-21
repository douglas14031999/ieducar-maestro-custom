<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixClassroomsTable extends Migration
{
    use MigrationUtils;
    use OwnDatabase;

    public function up(): void
    {
        if (Schema::hasTable('classrooms')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->integer('available');
                $table->boolean('multi');
            });

            return;
        }

        if (Schema::hasView('classrooms')) {
            $this->dropView('process_vacancy_statistics');
            $this->dropView('process_vacancy');
            $this->dropView('process_school');
            $this->dropView('classrooms');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/classrooms-2022-10-26.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/process_school-2020-09-22.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy-2022-10-11.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy_statistics-2020-11-26.sql');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('classrooms')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->dropColumn('available');
                $table->dropColumn('multi');
            });
        }
    }
}
