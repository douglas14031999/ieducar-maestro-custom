<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/search_students_by_name-2025-05-16.sql');
    }

    public function down(): void
    {
        $this->dropView('search_students_by_name');
    }
};
