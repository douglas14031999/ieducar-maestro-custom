<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\Process;
use Throwable;

class CopyProcess
{
    /**
     * @return Process
     *
     * @throws Throwable
     */
    private function copyProcess(int $id)
    {
        /** @var Process $processReference */
        $processReference = Process::findOrFail($id);
        $newProcess = new Process;

        $attributes = collect($processReference)->only([
            'name',
            'active',
            'school_year_id',
            'message_footer',
            'grade_age_range_link',
        ])->toArray();

        $attributes['name'] = '(Cópia) '. $attributes['name'];

        $attributes['grades'] = $processReference->grades()->get()->pluck('id');
        $attributes['periods'] = $processReference->periods()->get()->pluck('id');
        $fields = $processReference->fields()->get()->toArray();
        $stages = $processReference->stages()->get()->toArray();

        $newProcess->fill($attributes);
        $newProcess->saveOrFail();

        $newProcess->grades()->sync($attributes['grades']);
        $newProcess->periods()->sync($attributes['periods']);

        foreach ($fields as $field) {
            $newProcess->fields()->create([
                'process_id' => $newProcess->getKey(),
                'field_id' => $field['field_id'],
                'order' => $field['order'],
                'required' => $field['required'],
                'weight' => $field['weight'],
            ]);
        }

        foreach ($stages as $stage) {
            $newProcess->stages()->create([
                'process_id' => $newProcess->getKey(),
                'process_stage_type_id' => $stage['process_stage_type_id'],
                'name' => $stage['name'],
                'description' => $stage['description'],
                'start_at' => $stage['start_at'],
                'end_at' => $stage['end_at'],
                'radius' => $stage['radius'],
                'observation' => $stage['observation'],
                'renewal_at_same_school' => $stage['renewal_at_same_school'],
                'allow_waiting_list' => $stage['allow_waiting_list'],
            ]);
        }

        return $newProcess;
    }

    public function __invoke($_, array $args)
    {
        return $this->copyProcess($args['id']);
    }
}
