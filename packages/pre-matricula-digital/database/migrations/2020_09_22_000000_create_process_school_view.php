<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrationUtils;
use Illuminate\Database\Migrations\Migration;

class CreateProcessSchoolView extends Migration
{
    use MigrationUtils;

    public function up(): void
    {
        $this->dropView('public.process_school');
        $this->executeSqlFile(__DIR__ . '/../sqls/views/process_school-2020-09-22.sql');
    }

    public function down(): void
    {
        $this->dropView('public.process_school');
    }
}
