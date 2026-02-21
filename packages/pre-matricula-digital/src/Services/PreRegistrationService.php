<?php

namespace iEducar\Packages\PreMatricula\Services;

use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\ProcessField;
use iEducar\Packages\PreMatricula\Services\Weight\Weigher;
use Illuminate\Support\Str;

class PreRegistrationService
{
    private Weigher $weigher;

    public function __construct(Weigher $weigher)
    {
        $this->weigher = $weigher;
    }

    /**
     * Cria o protocolo pra uma pré-matrícula.
     */
    public function generateProtocol(): string
    {
        return Str::upper(Str::random('6'));
    }

    public function getWeightByField(ProcessField $processField, $value): int
    {
        $field = $processField->field;

        if ($processField->priority_field) {
            return 0;
        }

        if ($field->hasOptions()) {
            return $this->getWeightByFieldOption($field, $value, $processField->weight);
        }

        if ($field->isDate()) {
            return $this->weigher->date($value, $processField->weight);
        }

        return $this->weigher->filled($value, $processField->weight);
    }

    private function getWeightByFieldOption(Field $field, $value, $weight): int
    {
        $option = $field->options->where('id', $value)->first();

        return $option ? $option->weight * $weight : 0;
    }
}
