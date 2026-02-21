<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
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
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2022-01-03.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-12-09.sql');
    }

    public function down()
    {
        $this->dropView('public.report_preregistration');
        $this->dropView('public.exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2021-12-22.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-12-09.sql');
    }
};
