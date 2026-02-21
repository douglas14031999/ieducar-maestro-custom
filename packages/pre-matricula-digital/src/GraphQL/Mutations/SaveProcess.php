<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\ProcessSavedEvent;
use iEducar\Packages\PreMatricula\Models\Process;
use Throwable;

class SaveProcess
{
    /**
     * @return Process
     *
     * @throws Throwable
     */
    private function saveProcess(?int $id, array $attributes)
    {
        /** @var Process $process */
        $process = $id ? Process::findOrFail($id) : new Process;

        $attributes = collect($attributes)->only([
            'name',
            'active',
            'school_year_id',
            'message_footer',
            'grade_age_range_link',
            'force_suggested_grade',
            'show_priority_protocol',
            'allow_responsible_select_map_address',
            'block_incompatible_age_group',
            'auto_reject_by_days',
            'auto_reject_days',
            'selected_schools',
            'waiting_list_limit',
            'minimum_age',
            'one_per_year',
            'show_waiting_list',
            'reject_type_id',
            'criteria',
            'process_grouper_id',
        ])->toArray();

        $process->fill($attributes);
        $process->saveOrFail();

        event(new ProcessSavedEvent($process));

        return $process;
    }

    public function __invoke($_, array $args)
    {
        $process = $this->saveProcess($args['id'], $args);

        $process->grades()->sync($args['grades']);
        $process->periods()->sync($args['periods']);
        $process->schoolsSelected()->sync($args['schools']);

        return $process;
    }
}
