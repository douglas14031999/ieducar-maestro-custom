<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class RecreatePreregistrationPosition extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('preregistration_position');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/preregistration_position-2020-10-14.sql');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropView('preregistration_position');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/preregistration_position-2020-09-23.sql');
    }
}
