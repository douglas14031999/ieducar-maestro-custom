<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelinePreRegistrationListener
{
    public function handle(PreRegistrationUpdatedEvent $event): void
    {
        TimelineService::create(
            type: 'preregistration-updated',
            model: $event->preregistration,
            payload: [
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                ] : null,
                'before' => $event->before,
                'after' => $event->after,
            ]
        );
    }
}
