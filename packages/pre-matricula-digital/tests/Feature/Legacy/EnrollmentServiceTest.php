<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\StudentInepFactory;
use iEducar\Packages\PreMatricula\Exceptions\EnrollmentValidationException;
use iEducar\Packages\PreMatricula\Services\EnrollmentService;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateLegacyProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateLegacyProcess::class);

test('enrollment', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $service = app(EnrollmentService::class);

    $service->enroll($this->preregistration, $this->school->classrooms()->first());

    $this->assertDatabaseCount(LegacyRegistration::class, 1);
    $this->assertDatabaseCount(LegacyEnrollment::class, 1);
});

test('exists another enrollment', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentValidationException::ERROR_EXISTING_ENROLLMENT);

    $legacyStudent = LegacyStudentFactory::new()->create();

    $this->preregistration->update([
        'external_person_id' => $legacyStudent->ref_idpes,
    ]);

    LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    $service = app(EnrollmentService::class);

    $service->enroll($this->preregistration, $this->school->classrooms()->first());
});

test('require INEP number', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $this->legacyGrade->update([
        'exigir_inep' => true,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentValidationException::ERROR_MISSING_STUDENT_INEP);

    $legacyStudent = LegacyStudentFactory::new()->create();

    $this->preregistration->update([
        'external_person_id' => $legacyStudent->ref_idpes,
    ]);

    $service = app(EnrollmentService::class);

    $service->enroll($this->preregistration, $this->school->classrooms()->first());
});

test('with INEP number', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $this->legacyGrade->update([
        'exigir_inep' => true,
    ]);

    $legacyStudent = LegacyStudentFactory::new()->create();

    StudentInepFactory::new()->create([
        'student_id' => $legacyStudent,
    ]);

    $this->preregistration->update([
        'external_person_id' => $legacyStudent->ref_idpes,
    ]);

    $service = app(EnrollmentService::class);

    $service->enroll($this->preregistration, $this->school->classrooms()->first());

    $this->assertDatabaseCount(LegacyRegistration::class, 1);
    $this->assertDatabaseCount(LegacyEnrollment::class, 1);
});

test('block when no vacancy', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $classroom = $this->school->classrooms()->first();

    $this->legacySchoolClass->update([
        'max_aluno' => 0,
    ]);

    $this->legacySchoolGrade->update([
        'bloquear_enturmacao_sem_vagas' => true,
    ]);

    $legacyStudent = LegacyStudentFactory::new()->create();

    StudentInepFactory::new()->create([
        'student_id' => $legacyStudent,
    ]);

    $this->preregistration->update([
        'external_person_id' => $legacyStudent->ref_idpes,
    ]);

    $service = app(EnrollmentService::class);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentValidationException::ERROR_NO_VACANCY);

    $service->enroll($this->preregistration, $classroom);
});

test('enroll creating all person related records', function () {
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addAddressToResponsible();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $this->student->update([
        'phone' => '(00) 1234-5678',
    ]);

    $classroom = $this->school->classrooms()->first();

    $service = app(EnrollmentService::class);

    $service->enroll($this->preregistration, $classroom);

    $this->assertTrue(true);
});
