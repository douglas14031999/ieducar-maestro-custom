<?php

namespace iEducar\Packages\PreMatricula\Services;

use Carbon\Carbon;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

/**
 * @codeCoverageIgnore
 */
class TimelineService
{
    /**
     * Campos que devem ser excluídos do payload
     */
    private const EXCLUDED_FIELDS = [
        'created_at',
        'updated_at',
        'deleted_at',
        'slug',
        'latitude',
        'longitude',
        'manual_change_location',
    ];

    public function translatePayload(array $payload): array
    {
        if (isset($payload['before']) && isset($payload['after'])) {
            $payload['before'] = $this->translateFields($payload['before']);
            $payload['after'] = $this->translateFields($payload['after']);
        }

        return $payload;
    }

    public function translateFields(array $data): array
    {
        $translated = [];
        $translations = Lang::get('prematricula::pre-registration-timeline.fields', [], 'pt_BR');

        foreach ($data as $key => $value) {
            $translated[$translations[$key] ?? $key] = $value;
        }

        return $translated;
    }

    public function processFieldsWithOptions(\Illuminate\Support\Collection $fields): array
    {
        return $fields->mapWithKeys(function ($field) {
            $value = $field->value ?? 'Não informado';

            if ($field->field->field_type_id === Field::TYPE_CHECKBOX) {
                $value = $value == 'true' ? 'Sim' : 'Não';
            } elseif (in_array($field->field->field_type_id, [
                Field::TYPE_RADIO,
                Field::TYPE_SELECT,
                Field::TYPE_MULTI_SELECT,
            ])) {
                $option = $field->field->options->where('id', $value)->first();
                $value = $option ? $option->name : $value;
            }

            return [$field->field->name => $value];
        })->toArray();
    }

    public static function create(string $type, Model $model, ?array $payload): ?Timeline
    {
        $payload = self::clearPayload($payload);

        if (empty($payload)) {
            return null;
        }

        return Timeline::create([
            'type' => $type,
            'model_id' => $model->getKey(),
            'model_type' => get_class($model),
            'payload' => $payload,
        ]);
    }

    public static function clearPayload(array $payload): array
    {
        if (isset($payload['title'])) {
            $payload['title'] = array_filter($payload['title']);
        }

        if (isset($payload['before']) && isset($payload['after'])) {
            // Remove os campos excluídos primeiro
            $payload = self::removeExcludedFields($payload);

            // Trata os campos do modelo principal
            $modelBefore = array_diff_key($payload['before'], ['fields' => null]);
            $modelAfter = array_diff_key($payload['after'], ['fields' => null]);
            $changedModelKeys = array_keys(array_diff_assoc($modelAfter, $modelBefore));

            // Trata os campos extras (fields)
            $fieldsBefore = $payload['before']['fields'] ?? [];
            $fieldsAfter = $payload['after']['fields'] ?? [];
            $changedFieldKeys = array_keys(array_diff_assoc($fieldsAfter, $fieldsBefore));

            // Reconstrui o payload mantendo apenas as mudanças
            $payload['before'] = array_intersect_key($modelBefore, array_flip($changedModelKeys));
            $payload['after'] = array_intersect_key($modelAfter, array_flip($changedModelKeys));

            if (!empty($changedFieldKeys)) {
                $payload['before']['fields'] = array_intersect_key($fieldsBefore, array_flip($changedFieldKeys));
                $payload['after']['fields'] = array_intersect_key($fieldsAfter, array_flip($changedFieldKeys));
            }

            // Garante que os campos existam em ambos os lados
            $allKeys = array_unique(array_merge(
                array_keys($payload['before']),
                array_keys($payload['after'])
            ));

            $newBefore = [];
            $newAfter = [];

            foreach ($allKeys as $key) {
                $newBefore[$key] = $payload['before'][$key] ?? null;
                $newAfter[$key] = $payload['after'][$key] ?? null;
            }

            $payload['before'] = $newBefore;
            $payload['after'] = $newAfter;

            if (empty($payload['before']) || empty($payload['after'])) {
                return []; // limpa os dados extras que vem com before e after
            }
        }

        return $payload;
    }

    public static function removeExcludedFields(array $payload): array
    {
        foreach (self::EXCLUDED_FIELDS as $field) {
            unset($payload['before'][$field]);
            unset($payload['after'][$field]);
        }

        return $payload;
    }

    public static function formatDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }
}
