<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Models\ProcessGrouper;

class SaveProcessGrouper
{
    public function __invoke($_, array $args)
    {
        $attributes = collect($args)->only([
            'name',
            'waiting_list_limit',
        ])->toArray();

        /** @var ProcessGrouper $grouper */
        $grouper = ProcessGrouper::query()->create($attributes);

        if ($args['processes'] ?? []) {
            Process::query()->whereIn('id', $args['processes'])->update([
                'process_grouper_id' => $grouper->getKey(),
            ]);
        }

        return $grouper;
    }
}
