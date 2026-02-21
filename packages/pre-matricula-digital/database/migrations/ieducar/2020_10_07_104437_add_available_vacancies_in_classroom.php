<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;

class AddAvailableVacanciesInClassroom extends Migration
{
    use MigrationUtils;
    use OwnDatabase;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->useOwnDatabase()) {
            return;
        }

        $this->dropView('public.process_school');
        $this->dropView('public.process_vacancy');
        $this->dropView('public.classrooms');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/classrooms-2020-10-07.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_school-2020-09-22.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2020-09-22.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->useOwnDatabase()) {
            return;
        }

        $this->dropView('public.process_school');
        $this->dropView('public.process_vacancy');
        $this->dropView('public.classrooms');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/classrooms-2020-08-11.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_school-2020-09-22.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2020-09-22.sql');
    }
}
