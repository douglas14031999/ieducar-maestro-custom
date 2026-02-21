<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('registration_match');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/registration_match-2025-04-02.sql');
    }

    public function down(): void
    {
        $this->dropView('registration_match');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/registration_match-2024-03-14.sql');
    }
};
