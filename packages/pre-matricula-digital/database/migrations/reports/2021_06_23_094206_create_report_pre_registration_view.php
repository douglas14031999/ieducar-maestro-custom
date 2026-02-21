<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateReportPreRegistrationView extends Migration
{
    use MigrationUtils;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/public.report_preregistration-2021-06-23.sql');
    }

    public function down()
    {
        $this->dropView('public.report_preregistration');
    }
}
