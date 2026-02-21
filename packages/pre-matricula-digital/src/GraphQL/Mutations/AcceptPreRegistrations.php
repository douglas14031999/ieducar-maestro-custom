<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\Classroom;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\EnrollmentService;
use Nuwave\Lighthouse\Execution\ErrorPool;
use Throwable;

class AcceptPreRegistrations
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private ErrorPool $errors
    ) {}

    private function save(
        PreRegistration $preregistration,
        Classroom $classroom
    ) {
        $this->enrollmentService->enroll($preregistration, $classroom);

        $before = $preregistration->status;
        $preregistration->accept();
        $preregistration->classroom_id = $classroom->getKey();
        $preregistration->saveOrFail();

        event(new PreRegistrationStatusUpdatedEvent(
            preregistration: $preregistration,
            before: $before,
            after: PreRegistration::STATUS_ACCEPTED
        ));

        return $preregistration;
    }

    public function __invoke($_, array $args)
    {
        /** @var PreRegistration[] $preregistrations */
        $preregistrations = PreRegistration::query()
            ->with(['student', 'responsible', 'process', 'grade'])
            ->whereIn('id', $args['ids'])
            ->get();

        /** @var Classroom $classroom */
        $classroom = Classroom::query()->findOrFail($args['classroom']);

        $result = [];

        foreach ($preregistrations as $preregistration) {
            try {
                $result[] = $this->save($preregistration, $classroom);
            } catch (Throwable $error) {
                $this->errors->record($error);

                continue;
            }

            if ($preregistration->process->shouldNotReject()) {
                continue;
            }

            $cpf = $preregistration->student->cpf;

            // Caso não exista CPF, não faz o indeferimento automático, pois
            // irá indeferir todas as inscrições do processo
            if (empty($cpf)) {
                continue;
            }

            PreRegistration::query()
                ->whereHas('student', fn ($query) => $query->where('cpf', $cpf))
                ->when($preregistration->process->shouldRejectInSameProcess(), fn ($q) => $q->where('process_id', $preregistration->process_id))
                ->when($preregistration->process->shouldRejectInSameYear(), fn ($q) => $q->whereHas('process.schoolYear', fn ($r) => $r->where('year', $preregistration->process->schoolYear->year)))
                ->whereKeyNot($preregistration->id)
                ->get()
                ->map(function (PreRegistration $item) use ($preregistration) {
                    $item->reject("Indeferido devido ao protocolo #$preregistration->procotol ser deferido.");
                    $item->saveOrFail();
                });
        }

        return $result;
    }
}
