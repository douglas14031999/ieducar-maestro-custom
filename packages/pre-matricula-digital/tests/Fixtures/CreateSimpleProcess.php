<?php

namespace iEducar\Packages\PreMatricula\Tests\Fixtures;

use Database\Factories\CityFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PeriodFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PersonAddressFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PersonFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessPeriodFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessStageFactory;
use iEducar\Packages\PreMatricula\Database\Factories\SchoolFactory;
use iEducar\Packages\PreMatricula\Models\Course;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\Grade;
use iEducar\Packages\PreMatricula\Models\Period;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Models\ProcessStage;
use iEducar\Packages\PreMatricula\Models\School;
use iEducar\Packages\PreMatricula\Models\SchoolYear;

trait CreateSimpleProcess
{
    protected SchoolYear $year;

    protected Process $process;

    protected ProcessStage $stage;

    protected ProcessStage $waitingListStage;

    protected Period $period;

    protected Course $course;

    protected Grade $grade;

    protected School $school;

    protected Period $optionalPeriod;

    protected School $optionalSchool;

    protected Person $student;

    protected Person $responsible;

    protected PreRegistration $preregistration;

    public function createOpenedProcess(): void
    {
        $this->process = ProcessFactory::new()->complete()->create();

        $this->year = $this->process->schoolYear;
        $this->stage = $this->process->stages->first();
        $this->period = $this->process->periods->first();
        $this->grade = $this->process->grades->first();
        $this->course = $this->grade->course;
        $this->school = $this->process->schools->first();

        $this->optionalSchool = SchoolFactory::new()->create();
        $this->optionalPeriod = PeriodFactory::new()->create();

        ProcessPeriodFactory::new()->create([
            'process_id' => $this->process,
            'period_id' => $this->optionalPeriod,
        ]);
    }

    public function listenForNewPreregistration(): void
    {
        // TODO workaround
        // Foi preciso adicionar este listener para simular o comportamento da view `classrooms` que calcula
        // automaticamente o número de vagas, como nos testes esta é uma tabela física, é preciso manipular manualmente
        // a quantidade de vagas disponíveis
        PreRegistration::saved(function (PreRegistration $preregistration) {
            $preregistration->inClassroom->increment('available', -1);
        });
    }

    public function createPreRegistration(): void
    {
        $this->createPersons();

        $this->preregistration = PreRegistrationFactory::new()->create([
            'student_id' => $this->student,
            'responsible_id' => $this->responsible,
        ]);
    }

    public function createPersons(): void
    {
        $this->student = PersonFactory::new()->create([
            'name' => 'Portabilindo Junior',
            'date_of_birth' => now()->subYears(6),
            'cpf' => '046.161.240-27',
            'rg' => '9988776655',
            'birth_certificate' => '12345678901234567890123456789000',
        ]);

        $this->responsible = PersonFactory::new()->create([
            'name' => 'Portabilinda Mãe',
            'date_of_birth' => now()->subYears(36),
            'cpf' => '170.091.820-60',
            'rg' => '5544332211',
            'birth_certificate' => '99887766554433221100998877665544',
        ]);
    }

    public function addAddressToStudent(): void
    {
        $city = CityFactory::new()->create();

        PersonAddressFactory::new()->create([
            'person_id' => $this->student->getKey(),
            'city' => $city->name,
        ]);
    }

    public function addAddressToResponsible(): void
    {
        $city = CityFactory::new()->create();

        PersonAddressFactory::new()->create([
            'person_id' => $this->responsible->getKey(),
            'city' => $city->name,
        ]);
    }

    public function addWaitingListPeriod(): void
    {
        $this->waitingListStage = ProcessStageFactory::new()->create([
            'process_id' => $this->process,
            'process_stage_type_id' => ProcessStage::TYPE_WAITING_LIST,
        ]);
    }

    public function getMutation(): string
    {
        return '
            mutation NewPreRegistration(
                $input: PreRegistrationInput!
            ) {
                preregistrations: newPreRegistration(
                   input: $input
                ) {
                    process {
                        id
                    }
                }
            }
        ';
    }

    protected function getVariables(): array
    {
        return [
            'input' => [
                'process' => $this->process->getKey(),
                'stage' => $this->waitingListStage->getKey(),
                'type' => 'REGISTRATION',
                'grade' => $this->grade->getKey(),
                'period' => $this->period->getKey(),
                'school' => $this->school->getKey(),
                'optionalSchool' => null,
                'address' => [
                    'postalCode' => '00000-000',
                    'address' => 'Rua da Portabilis',
                    'number' => '123',
                    'complement' => null,
                    'neighborhood' => 'Centro',
                    'city' => 'Içara',
                    'lat' => 0,
                    'lng' => 0,
                    'manualChangeLocation' => false,
                ],
                'relationType' => 'MOTHER',
                'responsible' => [
                    [
                        'field' => Field::RESPONSIBLE_NAME,
                        'value' => 'Pai Responsável',
                    ],
                    [
                        'field' => Field::RESPONSIBLE_CPF,
                        'value' => '123.456.789-00',
                    ],
                    [
                        'field' => Field::RESPONSIBLE_DATE_OF_BIRTH,
                        'value' => '1980-01-01',
                    ],
                    [
                        'field' => Field::RESPONSIBLE_PHONE,
                        'value' => '(00) 00000-0000',
                    ],
                ],
                'student' => [
                    [
                        'field' => Field::STUDENT_NAME,
                        'value' => 'Aluno Portabilis',
                    ],
                    [
                        'field' => Field::STUDENT_DATE_OF_BIRTH,
                        'value' => '2010-01-01',
                    ],
                ],
            ],
        ];
    }

    protected function getVariablesForMultiple(): array
    {
        $variables = $this->getVariables();

        $variables['input']['waitingList'] = [
            [
                'period' => $this->period->getKey(),
                'school' => $this->school->getKey(),
            ],
            [
                'period' => $this->optionalPeriod->getKey(),
                'school' => $this->optionalSchool->getKey(),
            ],
        ];

        return $variables;
    }
}
