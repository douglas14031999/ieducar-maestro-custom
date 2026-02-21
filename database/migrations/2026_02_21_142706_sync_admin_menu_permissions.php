<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $adminType = 1;

        // Garantir que todos os menus tenham permissão para o administrador
        $menus = \Illuminate\Support\Facades\DB::table('menus')->get();

        foreach ($menus as $menu) {
            \Illuminate\Support\Facades\DB::table('pmieducar.menu_tipo_usuario')->updateOrInsert(
                [
                    'ref_cod_tipo_usuario' => $adminType,
                    'menu_id' => $menu->id,
                ],
                [
                    'visualiza' => 1,
                    'cadastra' => 1,
                    'exclui' => 1,
                ]
            );
        }

        // Limpar o cache de menus para garantir que as alterações apareçam imediatamente
        try {
            $client = config('legacy.app.database.dbname');
            \Illuminate\Support\Facades\Cache::tags(['menus', $client])->flush();
        } catch (\Exception $e) {
            // Ignorar erro se o driver de cache não suportar tags
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
