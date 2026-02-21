<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('process_school');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_school-2024-03-06.sql');
    }

    public function down(): void
    {
        $this->dropView('process_school');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_school-2020-09-22.sql');
    }
};
