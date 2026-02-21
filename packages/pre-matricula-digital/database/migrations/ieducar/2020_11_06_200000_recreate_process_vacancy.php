<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class RecreateProcessVacancy extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2020-11-06.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('process_vacancy_statistics');
        $this->dropView('process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2020-10-12.sql');
    }
}
