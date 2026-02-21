<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Support\ParseFields;

class UpdateAddress
{
    use ParseFields;

    public function __construct() {}

    public function __invoke($_, array $args): PreRegistration
    {
        /** @var PreRegistration $preregistration */
        $preregistration = PreRegistration::query()->where('protocol', $args['protocol'])->firstOrFail();

        $address = $preregistration->responsible->addresses()->first();
        $before = $address->toArray();

        $address->update($args['address']);

        event(new PreRegistrationAddressUpdatedEvent(
            preregistration: $preregistration,
            before: $before,
            after: $address->toArray()
        ));

        return $preregistration;
    }
}
