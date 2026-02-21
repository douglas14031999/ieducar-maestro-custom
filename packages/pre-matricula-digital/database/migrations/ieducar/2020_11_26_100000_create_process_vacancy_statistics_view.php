<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class CreateProcessVacancyStatisticsView extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'process_vacancy_statistics';

    protected $viewFile = __DIR__ . '/../../sqls/views/process_vacancy_statistics-2020-11-26.sql';

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
