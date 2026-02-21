<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\PriorityCalculator;
use iEducar\Packages\PreMatricula\Support\ParseFields;

class UpdatePreRegistration
{
    use ParseFields;

    public function __construct(
        private readonly PriorityCalculator $priority,
    ) {}

    public function __invoke($_, array $args): PreRegistration
    {
        /** @var PreRegistration $preregistration */
        $preregistration = PreRegistration::query()
            ->with(['grade', 'school', 'period'])
            ->where('protocol', $args['protocol'])
            ->firstOrFail();

        $before = [
            'grade' => $preregistration->grade->name,
            'school' => $preregistration->school->name,
            'period' => $preregistration->period->name,
        ];

        $preregistration->update([
            'period_id' => $args['period'],
            'school_id' => $args['school'],
            'grade_id' => $args['grade'],
            'in_classroom_id' => null,
        ]);

        $preregistration->load(['grade', 'school', 'period']);

        $this->priority->recalculatePriority($preregistration->process, $preregistration);

        $after = [
            'grade' => $preregistration->grade->name,
            'school' => $preregistration->school->name,
            'period' => $preregistration->period->name,
        ];

        event(new PreRegistrationUpdatedEvent(
            preregistration: $preregistration,
            before: $before,
            after: $after
        ));

        return $preregistration;
    }
}
