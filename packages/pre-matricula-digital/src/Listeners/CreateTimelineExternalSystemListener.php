<?php

namespace iEducar\Packages\PreMatricula\Listeners;

use iEducar\Packages\PreMatricula\Events\PreRegistrationExternalSystemUpdatedEvent;
use iEducar\Packages\PreMatricula\Services\TimelineService;

class CreateTimelineExternalSystemListener
{
    public function handle(PreRegistrationExternalSystemUpdatedEvent $event): void
    {
        $type = match ($event->type) {
            'address' => ['o endereço', 'bold'],
            'phones' => ['os telefones', 'bold'],
            'individual' => ['os dados pessoais', 'bold'],
            'documents' => ['os documentos', 'bold'],
            'name' => ['o nome', 'bold'],
            default => ''
        };

        if ($event->type === 'phones') {
            $event->before = $this->formatPhone($event->before);
            $event->after = $this->formatPhone($event->after);
        }

        TimelineService::create(
            type: "preregistration-external-system-{$event->type}-updated",
            model: $event->preregistration,
            payload: [
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                ] : null,
                'type' => $type,
                'before' => $event->before,
                'after' => $event->after,
            ]
        );
    }

    private function formatPhone(array $data): array
    {
        foreach (['phone', 'mobile'] as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $ddd = $data[$field]['ddd'];
                $number = $data[$field]['number'];

                // Formata o número com hífen antes dos 4 últimos dígitos
                $numberLength = strlen($number);
                $formattedNumber = $numberLength > 4
                    ? substr($number, 0, $numberLength - 4) . '-' . substr($number, -4)
                    : $number;

                $data[$field] = "({$ddd}) {$formattedNumber}";
            }
        }

        return $data;
    }
}
