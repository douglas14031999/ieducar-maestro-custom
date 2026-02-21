<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Events\PreRegistrationAddressUpdatedEvent;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelineAddressListener
{
    public function handle(PreRegistrationAddressUpdatedEvent $event): void
    {
        TimelineService::create(
            type: 'student-responsible-address-updated',
            model: $event->preregistration,
            payload: [
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                ] : null,
                'type' => 'endereço',
                'before' => $event->before,
                'after' => $event->after,
            ]
        );
    }
}
