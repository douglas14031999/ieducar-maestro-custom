<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up()
    {
        $this->dropView('process_vacancy_statistics');
        $this->dropView('process_vacancy');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy-2022-01-21.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }

    public function down()
    {
        $this->dropView('process_vacancy_statistics');
        $this->dropView('process_vacancy');
        // Adiciona a mesma view para evitar problemas com a view materializada antiga
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy-2022-01-21.sql');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_vacancy_statistics-2020-11-26.sql');
    }
};
