<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;

class SaveConfig
{
    public function __invoke($_, array $args): bool
    {
        foreach ($args['input'] as $config) {
            DB::table('settings')->updateOrInsert([
                'key' => "prematricula.features.{$config['key']}",
            ], $this->transformConfig($config['key'], $config['value']));
        }

        return true;
    }

    private function transformConfig($key, $value)
    {
        $boolean = [
            'allow_preregistration_data_update',
            'allow_external_system_data_update',
            'allow_transfer_registration',
            'allow_vacancy_certificate',
        ];

        if (in_array($key, $boolean)) {
            return [
                'type' => 'boolean',
                'value' => $value === 'true',
            ];
        }

        $string = [
            'transfer_description',
        ];

        if (in_array($key, $string)) {
            return [
                'type' => 'string',
                'value' => $value,
            ];
        }

        return $value;
    }
}
