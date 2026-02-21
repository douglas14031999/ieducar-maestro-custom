<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationMatchTable extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.registration_match';

    protected $viewFile = __DIR__ . '/../../sqls/views/registration_match-2020-10-01.sql';

    public function createTable()
    {
        //
    }

    public function dropTable()
    {
        //
    }
}
