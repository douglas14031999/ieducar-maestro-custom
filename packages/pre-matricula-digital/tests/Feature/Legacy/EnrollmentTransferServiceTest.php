<?php

use App\Models\LegacyRegistration;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyTransferRequestFactory;
use iEducar\Packages\PreMatricula\Exceptions\TransferValidationException;
use iEducar\Packages\PreMatricula\Services\EnrollmentService;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateLegacyProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateLegacyProcess::class);

beforeEach(function () {
    config(['prematricula.features.allow_transfer_registration' => true]);
    $this->createOpenedProcess();
    $this->createPreRegistration();
    $this->addWaitingListPeriod();
    $this->createLegacyData();

    $this->legacyStudent = LegacyStudentFactory::new()->create();
    $this->preregistration->update([
        'external_person_id' => $this->legacyStudent->ref_idpes,
    ]);

    $this->classroom = $this->school->classrooms()->first();
    $this->service = app(EnrollmentService::class);
});

test('transfer registration when feature is enabled', function () {
    $institution = LegacyInstitutionFactory::new()->create();

    $otherSchool = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $institution->getKey(),
    ]);

    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $otherSchool->getKey(),
    ]);

    $this->assertDatabaseHas(LegacyRegistration::class, [
        'cod_matricula' => $existingRegistration->getKey(),
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $otherSchool->getKey(),
    ]);

    $newRegistration = $this->service->enroll($this->preregistration, $this->classroom);

    $this->assertDatabaseHas(LegacyRegistration::class, [
        'cod_matricula' => $existingRegistration->getKey(),
        'ref_ref_cod_escola' => $otherSchool->getKey(),
        'aprovado' => App_Model_MatriculaSituacao::TRANSFERIDO,
    ]);

    $existingRegistration->refresh();
    $this->assertNotNull(
        $existingRegistration->data_cancel,
        'A matrícula antiga não recebeu data de saída.'
    );

    $this->assertNotNull($newRegistration, 'Nova matrícula ativa não foi criada.');
    $this->assertEquals($this->legacyStudent->getKey(), $newRegistration->ref_cod_aluno);
    $this->assertEquals($this->preregistration->grade_id, $newRegistration->ref_ref_cod_serie);
    $this->assertEquals($this->preregistration->process->school_year_id, $newRegistration->ano);
    $this->assertEquals($this->preregistration->school_id, $newRegistration->ref_ref_cod_escola);
    $this->assertEquals(App_Model_MatriculaSituacao::EM_ANDAMENTO, $newRegistration->aprovado);

    $this->assertEquals(
        1,
        LegacyRegistration::where([
            'ref_cod_aluno' => $this->legacyStudent->getKey(),
            'ref_ref_cod_serie' => $this->preregistration->grade_id,
            'ano' => $this->preregistration->process->school_year_id,
            'ref_ref_cod_escola' => $this->preregistration->school_id,
            'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        ])->count()
    );
});

test('transfer registration with active transfer request throws exception', function () {
    $institution = LegacyInstitutionFactory::new()->create();

    $otherSchool = LegacySchoolFactory::new()->create([
        'ref_cod_instituicao' => $institution->getKey(),
    ]);

    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $otherSchool->getKey(),
    ]);

    LegacyTransferRequestFactory::new()->create([
        'ref_cod_matricula_saida' => $existingRegistration->getKey(),
        'ref_cod_escola_destino' => $this->preregistration->school_id,
        'ativo' => 1,
    ]);

    $this->expectException(TransferValidationException::class);
    $this->expectExceptionCode(TransferValidationException::ERROR_TRANSFER_EXISTS);

    $this->service->enroll($this->preregistration, $this->classroom);
});
