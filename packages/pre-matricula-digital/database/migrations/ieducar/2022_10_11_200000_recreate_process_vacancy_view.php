<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;
    use OwnDatabase;

    public function up(): void
    {
        if ($this->useOwnDatabase()) {
            return;
        }

        $this->dropView('public.process_vacancy_statistics');
        $this->dropView('public.process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2022-10-11.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }

    public function down(): void
    {
        if ($this->useOwnDatabase()) {
            return;
        }

        $this->dropView('public.process_vacancy_statistics');
        $this->dropView('public.process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy-2022-01-21.sql');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }
};
