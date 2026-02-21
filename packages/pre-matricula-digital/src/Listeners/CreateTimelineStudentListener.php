<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Abstracts\TranslateAttributesAbstract;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStudentUpdatedEvent;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelineStudentListener extends TranslateAttributesAbstract
{
    public function handle(PreRegistrationStudentUpdatedEvent $event): void
    {
        $this->formatEventAttributes($event);

        TimelineService::create(
            type: 'preregistration-student-updated',
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
