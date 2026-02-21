<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(
            '
                update people
                set external_person_id = p.external_person_id
                from people s
                inner join preregistrations p 
                on p.student_id = s.id 
                where people.id = s.id
                and p.external_person_id is not null
            '
        );
    }

    public function down(): void
    {
        DB::unprepared(
            '
                update people
                set external_person_id = null
                where people.external_person_id is not null
            '
        );
    }
};
