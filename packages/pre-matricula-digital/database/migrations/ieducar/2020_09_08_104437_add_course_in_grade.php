<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;

class AddCourseInGrade extends Migration
{
    use \iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
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
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-09-08.sql');
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

        $this->dropView('public.grades');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-08-11.sql');
    }
}
