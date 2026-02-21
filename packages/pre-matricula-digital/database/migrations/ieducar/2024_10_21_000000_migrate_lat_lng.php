<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            $this->sql()
        );
    }

    private function sql(): string
    {
        return <<<'SQL'
SET "audit.context" = '{"user_id" : 0, "user_name" : "Eder Soares", "origin": "Issue 5086" }';

update person_addresses
set 
	latitude = (select value::float from settings where "key" = 'prematricula.map.lat'),
	longitude = (select value::float from settings where "key" = 'prematricula.map.lng'),
	manual_change_location = true
where latitude is null or longitude is null;
SQL;
    }
};
