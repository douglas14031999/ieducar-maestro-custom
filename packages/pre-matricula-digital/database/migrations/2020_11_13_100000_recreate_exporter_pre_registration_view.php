<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class RecreateExporterPreRegistrationView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/exporter_preregistration-2020-10-30.sql');
    }
}
