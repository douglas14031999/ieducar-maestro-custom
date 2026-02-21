<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('process_vacancy_statistics');
        $this->dropView('process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy-2024-03-06.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }

    public function down(): void
    {
        $this->dropView('process_vacancy_statistics');
        $this->dropView('process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy-2022-10-11.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }
};
