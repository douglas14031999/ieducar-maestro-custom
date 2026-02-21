<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class RecreateRegistrationMatchTable extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('registration_match');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/registration_match-2020-10-21.sql');
    }

    public function down(): void
    {
        $this->dropView('registration_match');
        $this->executeSqlFile(__DIR__ . '/../../sqls/views/registration_match-2020-10-21.sql');
    }
}
