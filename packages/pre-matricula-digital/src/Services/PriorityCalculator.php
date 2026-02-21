<?php

namespace iEducar\Packages\PreMatricula\Services;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Database\Eloquent\Collection;

class PriorityCalculator
{
    public function __construct(
        private PreRegistrationService $service,
    ) {}

    public function recalculateForProcess(Process $process): void
    {
        $process->preregistrations()->chunkById(100, function (Collection $preregistrations) use ($process) {
            $preregistrations->each(function (PreRegistration $preregistration) use ($process) {
                $this->recalculatePriority($process, $preregistration);
            });
        });
    }

    public function getPriorityForPreregistration(Process $process, array $data): int
    {
        $fields = $process->fields()->with('field.options')->get();

        $priority = 0;

        $data = array_merge($data['student'], $data['responsible']);

        foreach ($fields as $field) {
            $fieldId = $field->field->internal ?: 'field_' . $field->field->getKey();

            $fieldKey = collect($data)->search(function ($requestField) use ($fieldId) {
                return $requestField['field'] == $fieldId && $requestField['value'];
            });

            if ($fieldKey === false) {
                continue;
            }

            $value = $data[$fieldKey]['value'];
            $priority += $this->service->getWeightByField($field, $value);
        }

        return $priority;
    }

    public function recalculatePriority(Process $process, PreRegistration $preregistration): void
    {
        $fields = $process->fields()->where('weight', '<>', 0)->get();
        $fieldsWithPriority = $preregistration->fields()->whereIn('field_id', $fields->pluck('field_id'))->get();

        if ($fieldsWithPriority->isEmpty()) {
            return;
        }

        $priority = 0;

        if ($process->priority_custom) {
            $incomeField = $fields->first(fn ($field) => $field['priority_field'] === 'income');
            $membersField = $fields->first(fn ($field) => $field['priority_field'] === 'members');
            $multiplierField = $fields->first(fn ($field) => $field['priority_field'] === 'multiplier');

            $incomeValue = $fieldsWithPriority->first(fn ($field) => $field->field_id === $incomeField->field_id);
            $familyMembersValue = $fieldsWithPriority->first(fn ($field) => $field->field_id === $membersField->field_id);
            $multiplierOption = $fieldsWithPriority->first(fn ($field) => $field->field_id === $multiplierField->field_id);
            $multiplierValue = $multiplierField->field->options->first(fn ($option) => $option->id == $multiplierOption->value);

            // Se a família não tiver renda, sua pontuação será de 1 milhão.
            // Se a família tiver renda, o valor será subtraído de 1 milhão para que a família que possuir a menor renda
            // tenha o maior número de pontos no final
            $income = 0;
            if (!empty($incomeValue->value)) {
                $income = str($incomeValue->value)->match('/\.[0-9]{1,2}$/')->value()
                    ? (float) $incomeValue->value
                    : (float) str_replace(',', '.', str_replace('.', '', $incomeValue->value));
            }
            $members = $familyMembersValue->value ?: 1; // Sempre deve ser diferente de 0
            $perCapita = $income / $members;
            $value = $perCapita * $multiplierValue->weight;

            $priority = 1_000_000 - $value;

            if ($priority < 0) {
                $priority = 0;
            }
        }

        foreach ($fieldsWithPriority as $fieldWithPriority) {
            $field = $fields->first(fn ($field) => $field->field_id === $fieldWithPriority->field_id);
            $priority += $this->service->getWeightByField($field, $fieldWithPriority->value);
        }

        $preregistration->update([
            'priority' => (int) $priority,
        ]);
    }
}
