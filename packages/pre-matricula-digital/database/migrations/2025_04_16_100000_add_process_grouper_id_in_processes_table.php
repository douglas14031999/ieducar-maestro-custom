<?php

use iEducar\Packages\PreMatricula\Support\Database\OwnDatabase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use OwnDatabase;

    public function up(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->foreignId('process_grouper_id')->nullable();
        });

        // Evita erros ao executar testes no SQLite devido existir views dependentes da tabela `processes`
        if ($this->useOwnDatabase()) {
            return;
        }

        Schema::table('processes', function (Blueprint $table) {
            $table->foreign('process_grouper_id')->references('id')->on('process_grouper');
        });
    }

    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('process_grouper_id');
        });
    }
};
