<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class RecreateRegistrationMatch extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.registration_match';

    protected $viewFile = __DIR__ . '/../../sqls/views/registration_match-2020-11-18.sql';

    public function createTable()
    {
        //
    }

    public function dropTable()
    {
        //
    }
}
