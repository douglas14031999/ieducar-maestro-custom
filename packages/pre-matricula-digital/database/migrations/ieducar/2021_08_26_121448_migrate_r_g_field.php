<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('fields')
            ->where('field_type_id', 1)
            ->whereIn('internal', ['responsible_rg', 'student_rg'])
            ->update([
                'field_type_id' => 16,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('fields')
            ->where('field_type_id', 16)
            ->update([
                'field_type_id' => 1,
            ]);
    }
};
