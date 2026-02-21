<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;

class FixBirthRangeInGradeView extends Migration
{
    use MigrationUtils;
    use OwnDatabase;

    public function up()
    {
        if ($this->useThirdPartyDatabase()) {
            $this->dropView('public.grades');
            $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-10-05.sql');
        }
    }

    public function down()
    {
        if ($this->useThirdPartyDatabase()) {
            $this->dropView('public.grades');
            $this->executeSqlFile(__DIR__ . '/../../sqls/views/grades-2020-09-15.sql');
        }
    }
}
