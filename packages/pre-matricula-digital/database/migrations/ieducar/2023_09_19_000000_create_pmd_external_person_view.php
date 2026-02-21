<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreatePmdExternalPersonView extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('pmd_external_person');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/pmd_external_person-2023-09-19.sql');
    }

    public function down(): void
    {
        $this->dropView('pmd_external_person');
    }
}
