<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        if (Schema::hasTable('schools')) {
            return;
        }

        DB::unprepared($this->dropSql());

        $this->executeSqlFile(__DIR__.'/../sqls/views/schools.sql');
        $this->executeSqlFile(__DIR__.'/../sqls/views/exporter_preregistration.sql');
        $this->executeSqlFile(__DIR__.'/../sqls/views/report_preregistration.sql');

    }

    public function dropSql(): string
    {
        return <<<'SQL'
DROP VIEW IF EXISTS "report_preregistration";
DROP VIEW IF EXISTS "exporter_preregistration";
DROP VIEW IF EXISTS "schools";
SQL;
    }
};
