<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AddNewsStatusExporterPreRegistration extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('public.report_preregistration');
        $this->dropView('public.exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2021-09-14.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-06-23.sql');
    }

    public function down()
    {
        $this->dropView('public.report_preregistration');
        $this->dropView('public.exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2020-12-23.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-06-23.sql');
    }
}
