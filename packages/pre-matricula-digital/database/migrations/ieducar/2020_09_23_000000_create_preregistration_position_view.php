<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class CreatePreregistrationPositionView extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'preregistration_position';

    protected $viewFile = __DIR__ . '/../../sqls/views/preregistration_position-2020-09-23.sql';

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
