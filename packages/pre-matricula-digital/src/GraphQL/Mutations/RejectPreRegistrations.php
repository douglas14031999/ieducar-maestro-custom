<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

class RejectPreRegistrations
{
    public function __invoke($_, array $args)
    {
        /** @var PreRegistration[] $preregistrations */
        $preregistrations = PreRegistration::query()->whereIn('id', $args['ids'])->get();

        foreach ($preregistrations as $preregistration) {
            $before = $preregistration->status;
            $beforeJustification = $preregistration->observation;

            $preregistration->reject($args['justification'] ?? null);
            $preregistration->saveOrFail();

            event(new PreRegistrationStatusUpdatedEvent(
                preregistration: $preregistration,
                before: $before,
                after: PreRegistration::STATUS_REJECTED,
                beforeJustification: $beforeJustification,
                afterJustification: $args['justification'] ?? null
            ));
        }

        return $preregistrations;
    }
}
