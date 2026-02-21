<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsSchoolView extends Migration
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
        if ($this->useThirdPartyDatabase()) {
            $this->dropView('public.report_preregistration');
            $this->dropView('public.exporter_preregistration');
            $this->dropView('public.schools');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/public.schools-2021-08-18.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/exporter_preregistration-2020-12-23.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/public.report_preregistration-2021-06-23.sql');
        }

        if (Schema::hasTable('schools')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
            });
        }
    }

    public function down()
    {
        if ($this->useThirdPartyDatabase()) {
            $this->dropView('public.report_preregistration');
            $this->dropView('public.exporter_preregistration');
            $this->dropView('public.schools');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/schools-2020-08-11.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/exporter_preregistration-2020-12-23.sql');
            $this->executeSqlFile(__DIR__ . '/../sqls/views/public.report_preregistration-2021-06-23.sql');
        }

        if (Schema::hasTable('schools')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('phone');
            });

            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('mail');
            });
        }
    }
}
