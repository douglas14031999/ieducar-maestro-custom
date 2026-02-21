<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class CreateProcessVacancyView extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'process_vacancy';

    protected $viewFile = __DIR__ . '/../../sqls/views/process_vacancy-2020-09-22.sql';

    public function useThirdPartyDatabase()
    {
        return true;
    }

    public function createTable()
    {
        throw new Exception('Use view instead');
    }

    public function dropTable()
    {
        throw new Exception('Use view instead');
    }
}
