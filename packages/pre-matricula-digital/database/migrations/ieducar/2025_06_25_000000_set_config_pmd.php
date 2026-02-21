<?php

use App\Models\LegacyInstitution;
use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        /** @var LegacyInstitution $institution */
        $institution = LegacyInstitution::query()->where('ativo', 1)->first();

        if (empty($institution)) {
            return;
        }

        Setting::query()->updateOrCreate([
            'key' => 'prematricula.token',
        ], [
            'type' => 'string',
            'description' => 'Token de segurança do Pré-Matrícula Digital',
            'value' => Str::random(32),
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'prematricula.city',
        ], [
            'type' => 'string',
            'description' => 'Município',
            'value' => $institution->cidade,
        ]);

        Setting::query()->updateOrCreate([
            'key' => 'prematricula.state',
        ], [
            'type' => 'string',
            'description' => 'Sigla do estado (UF)',
            'value' => $institution->ref_sigla_uf,
        ]);
    }
};
