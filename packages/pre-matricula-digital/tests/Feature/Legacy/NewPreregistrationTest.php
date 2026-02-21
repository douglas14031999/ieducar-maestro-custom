<?php

use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use iEducar\Packages\PreMatricula\Models\ExternalPerson;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\Person;
use iEducar\Packages\PreMatricula\Support\OnlyNumbers;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateSimpleProcess::class);
uses(OnlyNumbers::class);

beforeEach(function () {
    config()->set('prematricula.legacy', true);
});

test('link external person by `CPF`', function () {
    $this->createOpenedProcess();
    $this->addWaitingListPeriod();

    $cpf = '987.654.321-00';

    $individual = LegacyIndividualFactory::new()->create([
        'cpf' => $this->onlyNumbers($cpf),
    ]);

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual->getKey(),
    ]);

    $variables = $this->getVariables();

    $variables['input']['student'][] = [
        'field' => Field::STUDENT_CPF,
        'value' => $cpf,
    ];

    $this->graphQL($this->getMutation(), $variables)->assertJson([
        'data' => [
            'preregistrations' => [
                [
                    'process' => [
                        'id' => $this->process->id,
                    ],
                ],
            ],
        ],
    ]);

    $this->assertDatabaseHas(Person::class, [
        'external_person_id' => $individual->getKey(),
    ]);

    $this->assertDatabaseHas(ExternalPerson::class, [
        'external_person_id' => $individual->getKey(),
        'cpf' => $cpf,
    ]);
});

test('link external person by `RG`', function () {
    $this->createOpenedProcess();
    $this->addWaitingListPeriod();

    $rg = '1234567890';

    $individual = LegacyIndividualFactory::new()
        ->withDocument(rg: $rg)
        ->create();

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual->getKey(),
    ]);

    $variables = $this->getVariables();

    $variables['input']['student'][] = [
        'field' => Field::STUDENT_RG,
        'value' => $rg,
    ];

    $this->graphQL($this->getMutation(), $variables)->assertJson([
        'data' => [
            'preregistrations' => [
                [
                    'process' => [
                        'id' => $this->process->id,
                    ],
                ],
            ],
        ],
    ]);

    $this->assertDatabaseHas(Person::class, [
        'external_person_id' => $individual->getKey(),
    ]);

    $this->assertDatabaseHas(ExternalPerson::class, [
        'external_person_id' => $individual->getKey(),
        'rg' => $rg,
    ]);
});

test('link external person by `birth certificate`', function () {
    $this->createOpenedProcess();
    $this->addWaitingListPeriod();

    $birthCertificate = '12345678901234567890123456789012';

    $individual = LegacyIndividualFactory::new()
        ->withDocument(birthCertificate: $birthCertificate)
        ->create();

    LegacyStudentFactory::new()->create([
        'ref_idpes' => $individual->getKey(),
    ]);

    $variables = $this->getVariables();

    $variables['input']['student'][] = [
        'field' => Field::STUDENT_BIRTH_CERTIFICATE,
        'value' => $birthCertificate,
    ];

    $this->graphQL($this->getMutation(), $variables)->assertJson([
        'data' => [
            'preregistrations' => [
                [
                    'process' => [
                        'id' => $this->process->id,
                    ],
                ],
            ],
        ],
    ]);

    $this->assertDatabaseHas(Person::class, [
        'external_person_id' => $individual->getKey(),
    ]);

    $this->assertDatabaseHas(ExternalPerson::class, [
        'external_person_id' => $individual->getKey(),
        'birth_certificate' => $birthCertificate,
    ]);
});
