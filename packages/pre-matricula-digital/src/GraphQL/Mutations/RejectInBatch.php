<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Models\PreRegistration;

class RejectInBatch
{
    public function __invoke($_, array $args)
    {
        $preRegistrations = PreRegistration::query()
            ->where(function ($builder) {
                $builder->waiting();
            })
            ->where('process_stage_id', $args['stageId'])
            ->where('process_id', $args['id'])
            ->get();

        foreach ($preRegistrations as $preRegistration) {
            $preRegistration->reject($args['justification']);
            $preRegistration->saveOrFail();
        }

        return $preRegistrations->count();
    }
}
