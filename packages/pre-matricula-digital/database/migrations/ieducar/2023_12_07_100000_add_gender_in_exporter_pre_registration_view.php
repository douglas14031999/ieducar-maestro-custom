<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class AddGenderInExporterPreRegistrationView extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('public.report_preregistration');
        $this->dropView('exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2023-12-07.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-12-09.sql');
    }

    public function down(): void
    {
        $this->dropView('public.report_preregistration');
        $this->dropView('exporter_preregistration');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/exporter_preregistration-2023-06-01.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-12-09.sql');
    }
}
