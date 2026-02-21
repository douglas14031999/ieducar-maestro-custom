<?php

namespace iEducar\Packages\PreMatricula\Support\Database;

use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
trait MigrationUtils
{
    public function createTriggerForDataChanges(string $trigger, string $function, string $table): void
    {
        $sql = <<<SQL
create trigger {$trigger}
    after insert or update or delete
    on {$table}
    for each row
execute procedure {$function}();
SQL;

        DB::unprepared($sql);
    }

    public function dropTriggerIfExists(string $trigger, string $table): void
    {
        DB::unprepared("drop trigger if exists $trigger on $table;");
    }

    public function dropFunctionIfExists(string $function): void
    {
        DB::unprepared("drop function if exists $function;");
    }

    public function dropView($view): void
    {
        DB::unprepared("DROP VIEW IF EXISTS {$view}");
    }

    public function dropMaterializedView($view): void
    {
        DB::unprepared("DROP MATERIALIZED VIEW IF EXISTS {$view}");
    }

    public function refreshMaterializedView($view): void
    {
        DB::unprepared("REFRESH MATERIALIZED VIEW {$view}");
    }

    public function executeSqlFile($filename): void
    {
        DB::unprepared(file_get_contents($filename));
    }
}
