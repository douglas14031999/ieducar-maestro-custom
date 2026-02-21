<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationResponsibleUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\PriorityCalculator;
use iEducar\Packages\PreMatricula\Services\TimelineService;
use iEducar\Packages\PreMatricula\Support\ParseFields;

class UpdateResponsible
{
    use ParseFields;

    public function __construct(
        private readonly PriorityCalculator $priority,
        private readonly TimelineService $timelineService,
    ) {}

    public function __invoke($_, array $args): PreRegistration
    {
        /** @var PreRegistration $preregistration */
        $preregistration = PreRegistration::query()->where('protocol', $args['protocol'])->firstOrFail();

        $data = $this->parseAttributesFromFields($args['fields'], 'responsible');

        $before = $preregistration->responsible->toArray();
        $beforeFields = $this->timelineService->processFieldsWithOptions(
            $preregistration->fields()->with('field')->get()
        );
        $before = array_merge($before, $beforeFields);

        $preregistration->responsible->update($data);

        $fields = $this->parseAttributesFromFields($args['fields'], 'field');

        foreach ($fields as $field => $value) {
            $preregistration->fields()->updateOrCreate([
                'field_id' => $field,
            ], [
                'value' => $value,
            ]);
        }

        $this->priority->recalculatePriority($preregistration->process, $preregistration);

        $after = $preregistration->responsible->toArray();
        $afterFields = $this->timelineService->processFieldsWithOptions(
            $preregistration->fields()->with('field')->get()
        );
        $after = array_merge($after, $afterFields);

        event(new PreRegistrationResponsibleUpdatedEvent(
            preregistration: $preregistration,
            before: $before,
            after: $after
        ));

        return $preregistration;
    }
}
