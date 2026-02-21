<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;

class CreateProcessGradeSuggest extends Migration
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

        $this->dropView('public.grades');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-10-06.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_grade_suggest-2020-10-06.sql');
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

        $this->dropView('public.process_grade_suggest');
        $this->dropView('public.grades');

        $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-10-05.sql');
    }
}
