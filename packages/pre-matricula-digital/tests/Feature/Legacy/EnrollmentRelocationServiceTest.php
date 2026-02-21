<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\User;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Packages\PreMatricula\Exceptions\EnrollmentRelocationValidationException;
use iEducar\Packages\PreMatricula\Services\EnrollmentService;
use iEducar\Packages\PreMatricula\Services\RegistrationTransferService;
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

    $this->legacySchoolClass->update([
        'max_aluno' => 2,
    ]);

    $this->classroom = $this->school->classrooms()->first();

    $user = LegacyUserFactory::new()->unique()->make();
    $user = User::find($user->id);
    $registrationService = new RegistrationTransferService;
    $enrollmentService = new \App\Services\EnrollmentService($user);

    $this->service = new EnrollmentService($registrationService, $enrollmentService);
});

test('relocation reuses registration when no enrollment is found', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
    ]);

    $newRegistration = $this->service->enroll($this->preregistration, $this->classroom);

    $this->assertEquals($existingRegistration->getKey(), $newRegistration->getKey());

    $this->assertDatabaseHas(LegacyRegistration::class, [
        'cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_aluno' => $this->legacyStudent->getKey(),
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ano' => $this->preregistration->process->school_year_id,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    $this->assertDatabaseHas(LegacyEnrollment::class, [
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'remanejado_mesma_turma' => false,
        'remanejado' => null,
        'ativo' => 1,
    ]);
});

test('relocation reuses registration when enrollment same classroom', function () {
    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
    ]);

    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_ENROLLMENT_SAME_CLASSROOM);

    $this->service->enroll($this->preregistration, $this->classroom);
});

test('relocation reuses registration when enrollment other classroom', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    $schoolClass = LegacySchoolClassFactory::new()->create();

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass->getKey(),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
    ]);

    $existingEnrollment = LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $schoolClass->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $activeEnrollments = LegacyEnrollment::where('ref_cod_matricula', $existingRegistration->getKey())->count();
    $this->assertEquals(1, $activeEnrollments, 'Deve haver uma enturmação para esta matrícula.');

    $newRegistration = $this->service->enroll($this->preregistration, $this->classroom);

    $this->assertEquals($existingRegistration->getKey(), $newRegistration->getKey());

    $this->assertDatabaseHas(LegacyRegistration::class, [
        'cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_aluno' => $this->legacyStudent->getKey(),
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ano' => $this->preregistration->process->school_year_id,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ]);

    $this->assertDatabaseHas(LegacyEnrollment::class, [
        'id' => $existingEnrollment->getKey(),
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $schoolClass->getKey(),
        'remanejado_mesma_turma' => false,
        'sequencial' => $existingEnrollment->sequencial,
        'remanejado' => true,
        'ativo' => 0,
    ]);

    $this->assertDatabaseHas(LegacyEnrollment::class, [
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'remanejado_mesma_turma' => false,
        'sequencial' => $existingEnrollment->sequencial + 1,
        'remanejado' => null,
        'ativo' => 1,
    ]);
});

test('relocation reuses registration when has multiples enrollments', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    $schoolClass1 = LegacySchoolClassFactory::new()->create();
    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass1->getKey(),
    ]);
    $schoolClass2 = LegacySchoolClassFactory::new()->create();
    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass2->getKey(),
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $schoolClass1->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $schoolClass2->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $activeEnrollments = LegacyEnrollment::where('ref_cod_matricula', $existingRegistration->getKey())->count();
    $this->assertEquals(2, $activeEnrollments, 'Deve haver duas enturmações para esta matrícula.');

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_MULTIPLE_ACTIVE_ENROLLMENTS);

    $this->service->enroll($this->preregistration, $this->classroom);
});

test('relocation after academic year', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
        'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
        'sequencial' => 1,
        'data_inicio' => now()->subDays(20),
        'data_fim' => now()->subDays(10),
        'dias_letivos' => 180,
    ]);

    // Cria a enturmação atual
    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_CANCELLATION_DATE_AFTER_ACADEMIC_YEAR);

    $this->service->enroll($this->preregistration, $this->classroom);
});

test('relocation before enrollment date', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
        'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
        'sequencial' => 1,
        'data_inicio' => now()->subDays(20),
        'data_fim' => now()->addDays(20),
        'dias_letivos' => 180,
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now()->addDay(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_PREVIOUS_CANCELLATION_DATE);

    $this->service->enroll($this->preregistration, $this->classroom);
});

test('relocation before registration date', function () {
    $existingRegistration = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now()->addDay(),
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
        'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
        'sequencial' => 1,
        'data_inicio' => now()->subDays(20),
        'data_fim' => now()->addDays(20),
        'dias_letivos' => 180,
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $existingRegistration->getKey(),
        'ref_cod_turma' => $this->classroom->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now()->subDay(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_PREVIOUS_ENROLL_CANCELLATION_DATE);

    $this->service->enroll($this->preregistration, $this->classroom);
});

test('relocation same time', function () {
    $this->legacySchoolClass->update([
        'ano' => $this->preregistration->process->school_year_id,
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
    ]);

    $this->legacySchoolClass->school->institution->update([
        'obrigar_campos_censo' => true,
    ]);

    $registration1 = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    $schoolClass1 = LegacySchoolClassFactory::new()->create([
        'ano' => $this->preregistration->process->school_year_id,
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass1->getKey(),
    ]);

    $registration2 = LegacyRegistrationFactory::new()->create([
        'ref_ref_cod_serie' => $this->preregistration->grade_id,
        'ref_cod_aluno' => $this->legacyStudent,
        'ano' => $this->preregistration->process->school_year_id,
        'ativo' => 1,
        'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_ref_cod_escola' => $this->preregistration->school_id,
        'data_matricula' => now(),
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $registration1->getKey(),
        'ref_cod_turma' => $schoolClass1->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $this->legacySchoolClass->getKey(),
        'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
        'sequencial' => 1,
        'data_inicio' => now()->subDays(20),
        'data_fim' => now()->addDays(20),
    ]);

    $schoolClass2 = LegacySchoolClassFactory::new()->create([
        'ano' => $this->legacySchoolClass->ano,
        'tipo_mediacao_didatico_pedagogico' => 1,
        'nao_informar_educacenso' => 0,
    ]);

    LegacySchoolClassStageFactory::new()->create([
        'ref_cod_turma' => $schoolClass2->getKey(),
        'ref_cod_modulo' => fn () => LegacyStageTypeFactory::new()->unique()->make(),
        'sequencial' => 1,
        'data_inicio' => now()->subDays(20),
        'data_fim' => now()->addDays(20),
    ]);

    LegacyEnrollment::create([
        'ref_cod_matricula' => $registration2->getKey(),
        'ref_cod_turma' => $schoolClass2->getKey(),
        'data_cadastro' => now(),
        'data_enturmacao' => now(),
        'ativo' => 1,
        'sequencial' => 1,
        'ref_usuario_cad' => 1,
        'sequencial_fechamento' => 1,
    ]);

    $this->expectExceptionMessage('Não foi possível realizar o deferimento');
    $this->expectExceptionCode(EnrollmentRelocationValidationException::ERROR_EXISTS_ACTIVE_ENROLLMENT_SAME_TIME);

    $this->service->enroll($this->preregistration, $this->classroom);
});
