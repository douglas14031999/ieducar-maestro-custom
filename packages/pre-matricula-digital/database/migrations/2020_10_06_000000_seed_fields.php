<?php

use iEducar\Packages\PreMatricula\Database\Seeds\FieldsTableSeeder;
use Illuminate\Database\Migrations\Migration;

class SeedFields extends Migration
{
    public function up()
    {
        (new FieldsTableSeeder)->run();
    }
}
