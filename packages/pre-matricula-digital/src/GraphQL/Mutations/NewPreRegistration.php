<?php

namespace iEducar\Packages\PreMatricula\GraphQL\Mutations;

use iEducar\Packages\PreMatricula\Exceptions\PreRegistrationValidationException;
use iEducar\Packages\PreMatricula\Models\Classroom;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Models\ProcessStage;
use iEducar\Packages\PreMatricula\Models\ProcessVacancy;
use iEducar\Packages\PreMatricula\Services\Concerns\LegacyPersonStudentFinder;
use iEducar\Packages\PreMatricula\Services\Concerns\PreRegistrationFinder;
use iEducar\Packages\PreMatricula\Services\PreRegistrationService;
use iEducar\Packages\PreMatricula\Services\PriorityCalculator;
use iEducar\Packages\PreMatricula\Support\ParseFields;
use Illuminate\Database\Eloquent\Collection;

class NewPreRegistration
{
    use ParseFields;

    public function __construct(
        private PreRegistrationService $service,
        private PreRegistrationFinder $finder,
        private PriorityCalculator $priority,
    ) {}

    /**
     * @throws PreRegistrationValidationException
     */
    public function __invoke(mixed $_, array $args): array
    {
        /** @var ProcessStage $stage */
        $stage = ProcessStage::query()
            ->with('process')
            ->findOrFail($args['process_stage_id']);

        $process = $stage->process;

        $this->validateIfProcessStageIsOpen($stage);
        $this->validateIfDoesNotHaveAnotherPreRegistration($args, $process);

        $student = $this->createPerson($args['student'], 'student', $args['address']);
        $responsible = $this->createPerson($args['responsible'], 'responsible', $args['address']);

        $args['priority'] = $this->priority->getPriorityForPreregistration($process, $args);
        $preregistrations = [];
        $preregistration = null;

        if (isset($args['school_id'])) {
            $preregistration = $this->createPreRegistration($args, $student, $responsible);

            $this->createFields($args['responsible'], $preregistration);
            $this->createFields($args['student'], $preregistration);

            $preregistrations[] = $preregistration;
        }

        if ($args['optionalSchool']) {
            $args['school_id'] = $args['optionalSchool'];
            $args['period_id'] = $args['optionalPeriod'];
            $args['preregistration_type_id'] = PreRegistration::WAITING_LIST;

            $optional = $this->createPreRegistration($args, $student, $responsible, $preregistration);

            $this->createFields($args['responsible'], $optional);
            $this->createFields($args['student'], $optional);

            $preregistrations[] = $optional;
        }

        foreach ($args['waitingList'] ?? [] as $waitingList) {
            $args['school_id'] = $waitingList['school_id'];
            $args['period_id'] = $waitingList['period_id'];
            $args['preregistration_type_id'] = PreRegistration::WAITING_LIST;

            $optional = $this->createPreRegistration($args, $student, $responsible, $preregistration);

            $this->createFields($args['responsible'], $optional);
            $this->createFields($args['student'], $optional);

            $preregistrations[] = $optional;
        }

        if (empty($preregistrations)) {
            throw PreRegistrationValidationException::nothingRegistered();
        }

        // TODO rever no futuro para calcular apenas 1 vez a prioridade
        if ($process->priority_custom) {
            foreach ($preregistrations as $preregistration) {
                $this->priority->recalculatePriority($process, $preregistration);
            }
        }

        return $preregistrations;
    }

    private function createPerson($fields, $prefix, $address): Person
    {
        $personData = $this->parseAttributesFromFields($fields, $prefix);

        // TODO #decouple
        // Vincula o cadastro do aluno do i-Educar ao cadastro do PMD para permitir a atualização dos dados

        if ($prefix === 'student' && config('prematricula.legacy')) {
            $finder = new LegacyPersonStudentFinder;

            $person = $finder->find($personData);

            if ($person) {
                $personData['external_person_id'] = $person->getKey();
            }
        }

        /** @var Person $person */
        $person = Person::create($personData);

        if (!empty($address)) {
            $person->addresses()->create($address);
        }

        return $person;
    }

    private function protocolExists(string $protocol): bool
    {
        return PreRegistration::query()->where('protocol', $protocol)->exists();
    }

    private function createPreRegistration(
        array $args,
        Person $student,
        Person $responsible,
        ?PreRegistration $preregistration = null
    ): PreRegistration {
        do {
            // Garante que o protocolo gerado é único no banco de dados.
            $protocol = $this->service->generateProtocol();
        } while ($this->protocolExists($protocol));

        $classroom = null;

        if ($args['preregistration_type_id'] !== PreRegistration::WAITING_LIST) {
            $this->validateIfHasVacancy($args);

            // As turmas regulares serão a primeira opção, caso não haja vaga na
            // turma regular a primeira turma multisseriada com vaga será escolhida

            $process = Process::query()->findOrFail($args['process_id']);

            $classroom = Classroom::query()
                ->where('school_year_id', $process->school_year_id)
                ->where('school_id', $args['school_id'])
                ->where('grade_id', $args['grade_id'])
                ->where('period_id', $args['period_id'])
                ->get()
                ->sortBy(fn ($classroom) => intval($classroom->multi))
                ->first(fn ($classroom) => $classroom->available > 0);
        }

        return PreRegistration::create([
            'preregistration_type_id' => $args['preregistration_type_id'],
            'parent_id' => $preregistration?->getKey(),
            'process_id' => $args['process_id'],
            'process_stage_id' => $args['process_stage_id'],
            'period_id' => $args['period_id'],
            'school_id' => $args['school_id'],
            'grade_id' => $args['grade_id'],
            'student_id' => $student->getKey(),
            'responsible_id' => $responsible->getKey(),
            'relation_type_id' => $args['relation_type_id'],
            'protocol' => $protocol,
            'code' => md5($protocol),
            'priority' => $args['priority'],
            'status' => PreRegistration::STATUS_WAITING,
            'external_person_id' => $args['external_person_id'] ?? $student->external_person_id ?? null,
            'in_classroom_id' => $classroom?->getKey(),
        ])->refresh();
    }

    private function createFields($fields, $preregistration): void
    {
        $fields = $this->parseAttributesFromFields($fields, 'field');

        foreach ($fields as $field => $value) {
            $preregistration->fields()->create([
                'field_id' => $field,
                'value' => $value,
            ]);
        }
    }

    /**
     * Valida se o período de inscrição está aberto.
     *
     * @throws PreRegistrationValidationException
     */
    private function validateIfProcessStageIsOpen(ProcessStage $stage): void
    {
        if ($stage->isClosed()) {
            throw PreRegistrationValidationException::stageIsNotOpen();
        }
    }

    /**
     * Valida se o aluno não possui uma pré-matrícula (rematrícula ou matrícula)
     * em situação de espera ou deferida.
     *
     * @throws PreRegistrationValidationException
     */
    private function validateIfDoesNotHaveAnotherPreRegistration(array $payload, Process $process): void
    {
        $data = $this->parseAttributesFromFields($payload['student'], 'student');

        $data['process_id'] = $payload['process_id'];
        $waitingList = $payload['preregistration_type_id'] === PreRegistration::WAITING_LIST;
        $notWaitingList = !$waitingList;

        /** @var Collection $collection */
        $collection = $this->finder->find($data);

        $countAccepted = $collection->filter(function (PreRegistration $preregistration) {
            return in_array($preregistration->status, [
                PreRegistration::STATUS_ACCEPTED,
            ]);
        })->count();

        $countWaiting = $collection->filter(function (PreRegistration $preregistration) {
            return in_array($preregistration->status, [
                PreRegistration::STATUS_WAITING,
            ]) && $preregistration->preregistration_type_id !== PreRegistration::WAITING_LIST;
        })->count();

        $count = $countAccepted + $countWaiting;

        if ($notWaitingList && $count > 0) {
            throw PreRegistrationValidationException::duplicatedRegistration();
        }

        $count = $collection->filter(function (PreRegistration $preregistration) {
            return in_array($preregistration->status, [
                PreRegistration::STATUS_WAITING,
                PreRegistration::STATUS_SUMMONED,
                PreRegistration::STATUS_IN_CONFIRMATION,
            ]) && $preregistration->preregistration_type_id === PreRegistration::WAITING_LIST;
        })->count();

        $waitingListLimit = $process->waiting_list_limit;
        $waitingListLimitInGrouper = $process->grouper?->waiting_list_limit ?? INF; // Infinito, se não definido
        $waitingListBeDoing = count($payload['waitingList'] ?? []);
        $waitingListBeDoing = $waitingList ? 1 + $waitingListBeDoing : $waitingListBeDoing;
        $waitingListTotal = $count + $waitingListBeDoing;

        if ($waitingList && $count >= $waitingListLimit) {
            throw PreRegistrationValidationException::waitingListLimit();
        }

        if ($waitingListTotal > $waitingListLimit) {
            throw PreRegistrationValidationException::waitingListLimit();
        }

        if ($waitingListTotal > $waitingListLimitInGrouper) {
            throw PreRegistrationValidationException::waitingListLimit();
        }
    }

    /**
     * Valida se há vagas para o turno, série, escola e processo específico.
     *
     * @throws PreRegistrationValidationException
     */
    private function validateIfHasVacancy(array $data): void
    {
        $vacancy = ProcessVacancy::query()
            ->where('process_id', $data['process_id'])
            ->where('school_id', $data['school_id'])
            ->where('grade_id', $data['grade_id'])
            ->where('period_id', $data['period_id'])
            ->first();

        if (empty($vacancy) || $vacancy->available < 1) {
            throw PreRegistrationValidationException::noVacancy();
        }
    }
}
