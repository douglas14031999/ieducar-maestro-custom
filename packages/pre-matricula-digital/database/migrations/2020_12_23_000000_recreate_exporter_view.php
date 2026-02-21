<?php

use iEducar\Packages\PreMatricula\Support\Database\MigrateDefiner;
use Illuminate\Database\Migrations\Migration;

class RecreateExporterView extends Migration
{
    use MigrateDefiner;

    protected $viewName = 'public.exporter_preregistration';

    protected $viewFile = __DIR__ . '/../sqls/views/exporter_preregistration-2020-12-23.sql';

    public function createTable()
    {
        //
    }

    public function dropTable()
    {
        //
    }
}
