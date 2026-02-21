<?php

namespace iEducar\Packages\PreMatricula\Services;

use App\Exceptions\Enrollment\CancellationDateAfterAcademicYearException;
use App\Exceptions\Enrollment\EnrollDateAfterAcademicYearException;
use App\Exceptions\Enrollment\ExistsActiveEnrollmentSameTimeException;
use App\Exceptions\Enrollment\PreviousCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollCancellationDateException;
use App\Exceptions\Enrollment\PreviousEnrollDateException;
use App\Exceptions\Enrollment\PreviousEnrollRegistrationDateException;
use App\Models\LegacyEnrollment;
use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App_Model_MatriculaSituacao;
use Carbon\Carbon;
use Exception;
use iEducar\Packages\PreMatricula\Exceptions\EnrollmentRelocationValidationException;
use iEducar\Packages\PreMatricula\Exceptions\EnrollmentValidationException;
use iEducar\Packages\PreMatricula\Models\Classroom;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Services\Concerns\FindOrCreatePerson;
use Illuminate\Support\Facades\DB;
use SequencialEnturmacao;

class EnrollmentService
{
    use FindOrCreatePerson;

    public function __construct(
        private RegistrationTransferService $registrationTransferService,
        private \App\Services\EnrollmentService $service) {}

    public function enroll(
        PreRegistration $preregistration,
        Classroom $classroom
    ) {
        $person = $this->getOrCreatePerson($preregistration);
        $student = $this->getOrCreateStudent($person, $preregistration);

        $this->validate($preregistration, $student, $classroom);

        $date = $classroom->getBeginAcademicYear();

        if ($date < now()) {
            $date = now();
        }

        $registration = $this->getRegistration($student, $preregistration);

        if ($this->shouldRejectExistingEnrollment($registration)) {
            throw EnrollmentValidationException::existingEnrollment($preregistration);
        }

        DB::beginTransaction();

        if ($this->canRelocateEnrollment($preregistration, $registration)) {
            $this->relocateActiveEnrollment($registration, $classroom, $date);
            $this->createRelocateEnrollment($registration, $classroom, $date);
            DB::commit();

            return $registration;
        }

        if ($this->canTransferRegistration($preregistration, $registration)) {
            $this->registrationTransferService->transfer($registration, $preregistration->school_id);
        }

        $registration = $this->createRegistration($student, $preregistration, $date);
        $this->createEnrollment($registration, $classroom, $date);

        DB::commit();

        return $registration;
    }

    private function transferEnabled()
    {
        return config('prematricula.features.allow_transfer_registration');
    }

    private function getRegistration(
        LegacyStudent $student,
        PreRegistration $preregistration,
    ) {
        return LegacyRegistration::query()
            ->with([
                'school:cod_escola,ref_cod_instituicao',
                'school.data',
                'activeEnrollments',
                'course:cod_curso,nm_curso',
                'grade:cod_serie,nm_serie,carga_horaria',
            ])
            ->where('ref_ref_cod_serie', $preregistration->grade_id)
            ->where('ref_cod_aluno', $student->getKey())
            ->where('ano', $preregistration->process->school_year_id)
            ->where('ativo', 1)
            ->where('aprovado', App_Model_MatriculaSituacao::EM_ANDAMENTO)
            ->first();
    }

    private function createRegistration(
        LegacyStudent $student,
        PreRegistration $preregistration,
        Carbon $date,
    ) {
        return LegacyRegistration::create(
            [
                'ref_ref_cod_serie' => $preregistration->grade_id,
                'ref_cod_aluno' => $student->getKey(),
                'ano' => $preregistration->process->school_year_id,
                'ref_usuario_cad' => 1, // todo: pegar usuário logado?
                'ativo' => 1,
                'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
                'ref_cod_curso' => $preregistration->grade->course_id,
                'ref_ref_cod_escola' => $preregistration->school_id,
                'data_matricula' => $date,
                'data_cadastro' => now(),
                'ultima_matricula' => 1,
            ]
        );
    }

    private function shouldRejectExistingEnrollment(?LegacyRegistration $registration)
    {
        return $registration !== null && !$this->transferEnabled();
    }

    private function canRelocateEnrollment(PreRegistration $preregistration, ?LegacyRegistration $registration)
    {
        return $registration !== null && $this->transferEnabled() &&
            $preregistration->school_id === $registration->ref_ref_cod_escola;
    }

    private function canTransferRegistration(PreRegistration $preregistration, ?LegacyRegistration $registration)
    {
        return $registration !== null && $this->transferEnabled() &&
            $preregistration->school_id !== $registration->ref_ref_cod_escola;
    }

    private function relocateActiveEnrollment(LegacyRegistration $registration, Classroom $classroom, Carbon $date)
    {
        $currentClassId = $classroom->getKey();
        $activeEnrollments = $registration
            ->activeEnrollments()
            ->get();

        if ($activeEnrollments->isEmpty()) {
            return false;
        }

        if ($activeEnrollments->count() > 1) {
            throw EnrollmentRelocationValidationException::multipleActiveEnrollments();
        }

        $activeEnrollment = $activeEnrollments->first();

        try {
            $this->service->cancelEnrollment($activeEnrollment, $date);
        } catch (CancellationDateAfterAcademicYearException $e) {
            throw EnrollmentRelocationValidationException::cancellationDateAfterAcademicYear($e->getMessage());
        } catch (PreviousCancellationDateException $e) {
            throw EnrollmentRelocationValidationException::previousCancellationDate($e->getMessage());
        } catch (PreviousEnrollCancellationDateException $e) {
            throw EnrollmentRelocationValidationException::previousEnrollCancellationDate($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        $previousEnrollment = $this->service->getPreviousEnrollmentAccordingToRelocationDate($registration);
        if (!$previousEnrollment) {
            return false;
        }

        $relocatedSameClassroom = $previousEnrollment->school_class_id === $currentClassId;

        if ($relocatedSameClassroom) {
            throw EnrollmentRelocationValidationException::enrollmentSameClassroom($classroom);
        }

        $activeEnrollment->update([
            'remanejado' => true,
            'abandono' => false,
            'transferido' => false,
            'reclassificado' => false,
            'falecido' => false,
        ]);

        $this->service->reorderSchoolClassAccordingToRelocationDate($previousEnrollment);
    }

    private function createRelocateEnrollment(LegacyRegistration $registration, Classroom $classroom, Carbon $date)
    {
        $schoolClass = LegacySchoolClass::query()->find($classroom->getKey());

        try {
            $this->service->enroll($registration, $schoolClass, $date);
        } catch (ExistsActiveEnrollmentSameTimeException $e) {
            throw EnrollmentRelocationValidationException::existsActiveEnrollmentSameTime($e->getMessage());
        } catch (EnrollDateAfterAcademicYearException $e) {
            throw EnrollmentRelocationValidationException::enrollDateAfterAcademicYear($e->getMessage());
        } catch (PreviousEnrollDateException $e) {
            throw EnrollmentRelocationValidationException::previousEnrollDate($e->getMessage());
        } catch (PreviousEnrollRegistrationDateException $e) {
            throw EnrollmentRelocationValidationException::previousEnrollRegistrationDate($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function createEnrollment(LegacyRegistration $registration, Classroom $classroom, Carbon $date)
    {
        $maxSequencial = LegacyEnrollment::where('ref_cod_matricula', $registration->getKey())->max('sequencial') ?: 1;

        $enrollmentSequence = new SequencialEnturmacao($registration->getKey(), $classroom->getKey(), now()->format('Y-m-d'));

        return LegacyEnrollment::firstOrCreate(
            [
                'ref_cod_matricula' => $registration->getKey(),
                'ref_cod_turma' => $classroom->getKey(),
            ],
            [
                'data_cadastro' => now(),
                'data_enturmacao' => $date,
                'ativo' => 1,
                'sequencial' => $maxSequencial,
                'ref_usuario_cad' => 1,
                'sequencial_fechamento' => $enrollmentSequence->ordenaSequencialNovaMatricula(),
            ]
        );
    }

    /**
     * @throws EnrollmentValidationException
     */
    private function validate(
        PreRegistration $preregistration,
        LegacyStudent $student,
        Classroom $classroom
    ) {
        $this->missingStudentInep($preregistration, $student);
        $this->noVacancy($preregistration, $classroom);
    }

    /**
     * @return void
     *
     * @throws EnrollmentValidationException
     */
    private function missingStudentInep(
        PreRegistration $preregistration,
        LegacyStudent $student
    ) {
        if ($student->inepNumber) {
            return;
        }

        $grade = LegacyGrade::query()->find($preregistration->grade_id);

        if ($grade->exigir_inep) {
            throw EnrollmentValidationException::missingStudentInep($preregistration);
        }
    }

    /**
     * @return void
     *
     * @throws EnrollmentValidationException
     */
    private function noVacancy(
        PreRegistration $preregistration,
        Classroom $classroom
    ) {
        /** @var LegacySchoolClass $classroom */
        $classroom = LegacySchoolClass::query()->findOrFail($classroom->getKey());

        if ($classroom->denyEnrollmentsWhenNoVacancy() && empty($classroom->vacancies)) {
            throw EnrollmentValidationException::noVacancy($preregistration);
        }
    }
}
