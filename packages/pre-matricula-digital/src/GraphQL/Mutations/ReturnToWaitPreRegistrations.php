<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

class ReturnToWaitPreRegistrations
{
    public function __invoke($_, array $args)
    {
        /** @var PreRegistration[] $preregistrations */
        $preregistrations = PreRegistration::query()->whereIn('id', $args['ids'])->get();

        foreach ($preregistrations as $preregistration) {
            $before = $preregistration->status;
            $preregistration->returnToWait();
            $preregistration->saveOrFail();

            event(new PreRegistrationStatusUpdatedEvent(
                preregistration: $preregistration,
                before: $before,
                after: PreRegistration::STATUS_WAITING
            ));
        }

        return $preregistrations;
    }
}
