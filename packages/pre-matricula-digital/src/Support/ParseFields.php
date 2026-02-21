<?php

namespace iEducar\Packages\PreMatricula\Support;

use Illuminate\Support\Str;

trait ParseFields
{
    private function parseAttributesFromFields($fields, $prefix): array
    {
        return collect($fields)->filter(function ($field) use ($prefix) {
            return Str::startsWith($field['field'], $prefix . '_'); // && $field['value'];
        })->mapWithKeys(function ($field) use ($prefix) {
            return [
                Str::substr($field['field'], Str::length($prefix) + 1) => $field['value'],
            ];
        })->toArray();
    }
}
