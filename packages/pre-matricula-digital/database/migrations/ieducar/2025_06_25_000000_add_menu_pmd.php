<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menus')->updateOrInsert([
            'process' => 56,
        ], [
            'title' => 'Pré-matrícula',
            'icon' => 'fa-share-square-o',
            'order' => 5,
            'type' => 1,
            'active' => true,
            'link' => '/pre-matricula-digital/inscricoes',
        ]);

        DB::table('menus')->updateOrInsert([
            'process' => 5656,
        ], [
            'parent_id' => Menu::query()->where('process', 56)->firstOrFail()->getKey(),
            'title' => 'Pré-matrícula',
            'link' => '#',
            'type' => 2,
            'active' => true,
        ]);
    }
};
