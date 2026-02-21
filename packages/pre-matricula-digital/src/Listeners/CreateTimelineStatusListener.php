<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelineStatusListener
{
    private const STATUS = [
        PreRegistration::STATUS_WAITING => 'Aguardando',
        PreRegistration::STATUS_ACCEPTED => 'Aceito',
        PreRegistration::STATUS_REJECTED => 'Rejeitado',
        PreRegistration::STATUS_SUMMONED => 'Convocado',
        PreRegistration::STATUS_IN_CONFIRMATION => 'Em Confirmação',
    ];

    public function handle(PreRegistrationStatusUpdatedEvent $event): void
    {
        $beforeStatusName = self::STATUS[$event->before] ?? 'Desconhecido';
        $afterStatusName = self::STATUS[$event->after] ?? 'Desconhecido';

        TimelineService::create(
            type: 'preregistration-status-updated',
            model: $event->preregistration,
            payload: [
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                ] : null,
                'before' => [
                    'status' => $beforeStatusName,
                    'justification' => $event->beforeJustification,
                ],
                'after' => [
                    'status' => $afterStatusName,
                    'justification' => $event->afterJustification,
                ],
            ]
        );
    }
}
