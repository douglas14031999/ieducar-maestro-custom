<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusAutoRejectedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelineStatusAutoRejectedListener
{
    private const STATUS = [
        PreRegistration::STATUS_WAITING => 'Aguardando',
        PreRegistration::STATUS_ACCEPTED => 'Aceito',
        PreRegistration::STATUS_REJECTED => 'Rejeitado',
        PreRegistration::STATUS_SUMMONED => 'Convocado',
        PreRegistration::STATUS_IN_CONFIRMATION => 'Em Confirmação',
    ];

    public function handle(PreRegistrationStatusAutoRejectedEvent $event): void
    {
        $beforeStatusName = self::STATUS[$event->before] ?? 'Desconhecido';
        $afterStatusName = self::STATUS[PreRegistration::STATUS_REJECTED] ?? 'Desconhecido';

        TimelineService::create(
            type: 'preregistration-status-auto-rejected',
            model: $event->preRegistration,
            payload: [
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
