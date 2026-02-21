<?php

namespace iEducar\Packages\PreMatricula\Console\Commands;

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
class PrematriculaInstallCommand extends Command
{
    use MigrationUtils;

    protected $signature = 'prematricula:install';

    protected $description = 'Install PMD database';

    public function handle(): int
    {
        DB::unprepared($this->dropSql());

        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/user.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/schools.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/user_school.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/periods.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/courses.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/school_years.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/classrooms.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/grades.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/process_school.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/process_vacancy.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/process_vacancy_statistics.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/exporter_preregistration.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/report_preregistration.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/process_grade_suggest.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/views/registration_match.sql');
        $this->executeSqlFile(__DIR__ . '/../../../database/sqls/insert_settings_key_logo.sql');

        return 0;
    }

    public function dropSql(): string
    {
        return <<<'SQL'
UPDATE settings set value = '1' where key = 'prematricula.active';

DROP VIEW IF EXISTS "process_vacancy_statistics";
DROP VIEW IF EXISTS "process_vacancy";
DROP VIEW IF EXISTS "process_school";
DROP VIEW IF EXISTS "report_preregistration";
DROP VIEW IF EXISTS "exporter_preregistration";

ALTER TABLE processes DROP CONSTRAINT IF EXISTS "processes_school_year_id_foreign";
ALTER TABLE preregistrations DROP CONSTRAINT IF EXISTS "preregistrations_grade_id_foreign";
ALTER TABLE preregistrations DROP CONSTRAINT IF EXISTS "preregistrations_period_id_foreign";
ALTER TABLE preregistrations DROP CONSTRAINT IF EXISTS "preregistrations_school_id_foreign";
ALTER TABLE process_period DROP CONSTRAINT IF EXISTS "process_period_period_id_foreign";
ALTER TABLE process_grade DROP CONSTRAINT IF EXISTS "process_grade_grade_id_foreign";

DROP TABLE IF EXISTS "user_school";
DROP TABLE IF EXISTS "user";
DROP TABLE IF EXISTS "classrooms";
DROP TABLE IF EXISTS "school_years";
DROP TABLE IF EXISTS "schools";
DROP TABLE IF EXISTS "periods";
DROP TABLE IF EXISTS "courses";
DROP TABLE IF EXISTS "grades";

DELETE FROM public.settings WHERE key = 'prematricula.logo';
SQL;
    }
}
