<?php

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\SchoolYearFactory;
use iEducar\Packages\PreMatricula\Models\ProcessStage;
use iEducar\Packages\PreMatricula\Support\InitialsFromName;
use iEducar\Packages\PreMatricula\Support\OnlyNumbers;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateLegacyProcess;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateSimpleProcess::class);
uses(CreateLegacyProcess::class);
uses(InitialsFromName::class);
uses(OnlyNumbers::class);

beforeEach(function () {
    $this->query = '
        query matches(
            $stage: ID!
            $name: String
            $dateOfBirth: Date
            $cpf: String
            $rg: String
            $birthCertificate: String
        ) {
            matches: getStudentMatches(
                stage: $stage
                name: $name
                dateOfBirth: $dateOfBirth
                cpf: $cpf
                rg: $rg
                birthCertificate: $birthCertificate
            ) {
                id
                initials
                dateOfBirth
                registration {
                    year
                    school {
                        id
                        name
                    }
                    grade {
                        id
                        name
                    }
                    period {
                        id
                        name
                    }
                }
            }
        }
    ';

    $this->createOpenedProcess();
    $this->createPreRegistration();
});

test('does not matches without restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [],
        ],
    ]);
});

test('matches registration last year restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->stage->update([
        'restriction_type' => ProcessStage::RESTRICTION_REGISTRATION_LAST_YEAR,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);

    $this->createLegacyRegistrationLastYear();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->legacyIndividual->getKey(),
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                    'registration' => [
                        'year' => $this->legacyRegistrationLastYear->ano,
                        'school' => [
                            'id' => $this->legacyRegistrationLastYear->school->id,
                            'name' => $this->legacyRegistrationLastYear->school->name,
                        ],
                        'grade' => [
                            'id' => $this->legacyRegistrationLastYear->grade->id,
                            'name' => $this->legacyRegistrationLastYear->grade->name,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

test('matches registration current year restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->stage->update([
        'restriction_type' => ProcessStage::RESTRICTION_REGISTRATION_CURRENT_YEAR,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);

    $this->createLegacyRegistrationCurrentYear();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->legacyIndividual->getKey(),
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                    'registration' => [
                        'year' => $this->legacyRegistrationLastYear->ano,
                        'school' => [
                            'id' => $this->legacyRegistrationLastYear->school->id,
                            'name' => $this->legacyRegistrationLastYear->school->name,
                        ],
                        'grade' => [
                            'id' => $this->legacyRegistrationLastYear->grade->id,
                            'name' => $this->legacyRegistrationLastYear->grade->name,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

test('matches no exists registration in current year restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->stage->update([
        'restriction_type' => ProcessStage::RESTRICTION_NO_REGISTRATION_CURRENT_YEAR,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);

    $this->createLegacyRegistrationCurrentYear();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->legacyIndividual->getKey(),
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                    'registration' => [
                        'year' => $this->legacyRegistrationLastYear->ano,
                        'school' => [
                            'id' => $this->legacyRegistrationLastYear->school->id,
                            'name' => $this->legacyRegistrationLastYear->school->name,
                        ],
                        'grade' => [
                            'id' => $this->legacyRegistrationLastYear->grade->id,
                            'name' => $this->legacyRegistrationLastYear->grade->name,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

test('matches no exists registration in same period and current year restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->stage->update([
        'restriction_type' => ProcessStage::RESTRICTION_NO_REGISTRATION_PERIOD_CURRENT_YEAR,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);

    $this->createLegacyRegistrationCurrentYear();

    $this->legacyRegistrationLastYear->lastEnrollment->update([
        'turno_id' => $this->period->id,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->legacyIndividual->getKey(),
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                    'registration' => [
                        'year' => $this->legacyRegistrationLastYear->ano,
                        'school' => [
                            'id' => $this->legacyRegistrationLastYear->school->id,
                            'name' => $this->legacyRegistrationLastYear->school->name,
                        ],
                        'grade' => [
                            'id' => $this->legacyRegistrationLastYear->grade->id,
                            'name' => $this->legacyRegistrationLastYear->grade->name,
                        ],
                        'period' => [
                            'id' => $this->legacyPeriod->id,
                            'name' => $this->legacyPeriod->name,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});

test('matches new student restriction', function () {
    $this->createLegacyData();
    $this->createLegacyPersonData();

    $this->stage->update([
        'restriction_type' => ProcessStage::RESTRICTION_NEW_STUDENT,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->legacyIndividual->getKey(),
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                ],
            ],
        ],
    ]);

    $this->preregistration->delete();
    $this->legacyStudent->delete();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);
});

test('matches new student with preregistration restriction', function () {
    $this->process->update([
        'one_per_year' => true,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJson([
        'data' => [
            'matches' => [
                [
                    'id' => $this->student->id,
                    'initials' => $this->initials($this->student->name),
                    'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                ],
            ],
        ],
    ]);

    $this->preregistration->delete();

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);
});

test('does not match with preregistration in other years', function () {
    $this->preregistration->delete();

    $this->process->update([
        'one_per_year' => true,
    ]);

    PreRegistrationFactory::new()->create([
        'process_id' => ProcessFactory::new()->complete()->create([
            'school_year_id' => SchoolYearFactory::new()->create([
                'name' => now()->subYear()->year,
                'year' => now()->subYear()->year,
            ]),
        ]),
        'student_id' => $this->student,
        'responsible_id' => $this->responsible,
    ]);

    $this->graphQL($this->query, [
        'stage' => $this->stage->id,
        'name' => $this->student->name,
        'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        'cpf' => $this->student->cpf,
        'rg' => $this->student->rg,
        'birthCertificate' => $this->student->birth_certificate,
    ])->assertJsonPath('data.matches', []);
});
