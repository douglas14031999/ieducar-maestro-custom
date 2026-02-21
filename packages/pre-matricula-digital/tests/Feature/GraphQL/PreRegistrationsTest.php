<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class PreRegistrationsTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_create_pre_registration(): void
    {
        $input = $this->prepareToTest();

        $query = '
          mutation NewPreRegistration(
            $input: PreRegistrationInput!
          ) {
            preregistrations: newPreRegistration(
             input: $input
            ) {
              school {
                id
              }
            }
          }
        ';

        $response = $this->graphQL($query, [
            'input' => $input,
        ]);
        $response->assertStatus(200);
        $schoolId = (int) current($response->json('data.preregistrations.*.school.id'));
        $this->assertSame(
            $this->school->id,
            $schoolId
        );
    }

    public function test_create_pre_registration_with_optional_school(): void
    {
        $input = $this->prepareToTest();

        $input['optionalSchool'] = $this->optionalSchool->id;
        $input['optionalPeriod'] = $this->optionalPeriod->id;

        $query = '
          mutation NewPreRegistration(
            $input: PreRegistrationInput!
          ) {
            preregistrations: newPreRegistration(
             input: $input
            ) {
              school {
                id
              }
            }
          }
        ';

        $response = $this->graphQL($query, [
            'input' => $input,
        ]);
        $response->assertStatus(200);
        $schoolId = (int) current($response->json('data.preregistrations.*.school.id'));
        $this->assertSame(
            $this->school->id,
            $schoolId
        );
    }

    public function test_pre_registration_out_of_opened_stage(): void
    {
        $input = $this->prepareToTest();

        $this->stage->update([
            'start_at' => now()->addDay(),
        ]);

        $query = '
          mutation NewPreRegistration(
            $input: PreRegistrationInput!
          ) {
            preregistrations: newPreRegistration(
             input: $input
            ) {
              school {
                id
              }
            }
          }
        ';

        $response = $this->graphQL($query, [
            'input' => $input,
        ]);

        $response->assertStatus(200)
            ->assertGraphQLValidationError('message', 'O período de inscrição não está aberto');
    }

    public function test_sort_by_date(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create();

        $second->forceFill([
            'created_at' => now()->subMonth(),
        ])->save();

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $sort: PreRegistrationSort
            ) {
                preregistrations(
                    sort: $sort
                ) {
                    data {
                        id
                        protocol
                        student {
                            name
                            dateOfBirth
                        }
                        position
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'sort' => 'DATE',
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'protocol' => $second->protocol,
                            'student' => [
                                'name' => $second->student->name,
                                'dateOfBirth' => $second->student->date_of_birth->format('Y-m-d'),
                            ],
                            'position' => $second->position,
                        ],
                        [
                            'id' => (string) $first->id,
                            'protocol' => $first->protocol,
                            'student' => [
                                'name' => $first->student->name,
                                'dateOfBirth' => $first->student->date_of_birth->format('Y-m-d'),
                            ],
                            'position' => $first->position,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_sort_by_position(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create([
            'priority' => 1,
            'process_id' => $first->process_id,
            'process_stage_id' => $first->process_stage_id,
            'period_id' => $first->period_id,
            'school_id' => $first->school_id,
            'grade_id' => $first->grade_id,
        ]);

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $sort: PreRegistrationSort
            ) {
                preregistrations(
                    sort: $sort
                ) {
                    data {
                        id
                        protocol
                        student {
                            name
                            dateOfBirth
                        }
                        position
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'sort' => 'POSITION',
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'protocol' => $second->protocol,
                            'student' => [
                                'name' => $second->student->name,
                                'dateOfBirth' => $second->student->date_of_birth->format('Y-m-d'),
                            ],
                            'position' => $second->position,
                        ],
                        [
                            'id' => (string) $first->id,
                            'protocol' => $first->protocol,
                            'student' => [
                                'name' => $first->student->name,
                                'dateOfBirth' => $first->student->date_of_birth->format('Y-m-d'),
                            ],
                            'position' => $first->position,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_sort_by_school(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create();

        $second->school->name = 'AAA School';
        $second->school->save();

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $sort: PreRegistrationSort
            ) {
                preregistrations(
                    sort: $sort
                ) {
                    data {
                        id
                        school {
                            name
                        }
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'sort' => 'SCHOOL',
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'school' => [
                                'name' => $second->school->name,
                            ],
                        ],
                        [
                            'id' => (string) $first->id,
                            'school' => [
                                'name' => $first->school->name,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_sort_by_student(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create();

        $second->student->name = 'AAA Fulano';
        $second->student->save();

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $sort: PreRegistrationSort
            ) {
                preregistrations(
                    sort: $sort
                ) {
                    data {
                        id
                        student {
                            name
                        }
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'sort' => 'NAME',
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'student' => [
                                'name' => $second->student->name,
                            ],
                        ],
                        [
                            'id' => (string) $first->id,
                            'student' => [
                                'name' => $first->student->name,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_sort_by_date_of_birth(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create();

        $second->student->date_of_birth = now()->subYears(10);
        $second->student->save();

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $sort: PreRegistrationSort
            ) {
                preregistrations(
                    sort: $sort
                ) {
                    data {
                        id
                        student {
                            name
                        }
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'sort' => 'DATE_OF_BIRTH',
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'student' => [
                                'name' => $second->student->name,
                            ],
                        ],
                        [
                            'id' => (string) $first->id,
                            'student' => [
                                'name' => $first->student->name,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_search(): void
    {
        $first = PreRegistrationFactory::new()->create();
        $second = PreRegistrationFactory::new()->create([
            'priority' => 1,
            'process_id' => $first->process_id,
            'process_stage_id' => $first->process_stage_id,
            'period_id' => $first->period_id,
            'school_id' => $first->school_id,
            'grade_id' => $first->grade_id,
        ]);

        $first->refresh();
        $second->refresh();

        $query = '
            query preregistrations(
                $search: String
            ) {
                preregistrations(
                    search: $search
                ) {
                    data {
                        id
                        protocol
                        student {
                            name
                            dateOfBirth
                        }
                        position
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'search' => $second->protocol,
        ])->assertExactJson([
            'data' => [
                'preregistrations' => [
                    'data' => [
                        [
                            'id' => (string) $second->id,
                            'protocol' => $second->protocol,
                            'student' => [
                                'name' => $second->student->name,
                                'dateOfBirth' => $second->student->date_of_birth->format('Y-m-d'),
                            ],
                            'position' => $second->position,
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function prepareToTest(): array
    {
        $this->createOpenedProcess();
        $this->createPersons();

        return [
            'type' => 'REGISTRATION',
            'process' => $this->process->id,
            'stage' => $this->stage->id,
            'grade' => $this->grade->id,
            'period' => $this->period->id,
            'school' => $this->school->id,
            'optionalSchool' => null,
            'optionalPeriod' => null,
            'student' => [
                ['field' => 'student_date_of_birth', 'value' => $this->student->date_of_birth],
                ['field' => 'student_name', 'value' => $this->student->name],
            ],
            'address' => [
                'address' => 'Rua 10 de Maio',
                'city' => 'Criciúma',
                'cityIbgeCode' => 4204608,
                'complement' => '',
                'lat' => -28.698828,
                'lng' => -49.4542268,
                'neighborhood' => 'São Defende',
                'number' => '123',
                'postalCode' => '88808-043',
                'stateAbbreviation' => 'SC',
                'manualChangeLocation' => false,
            ],
            'responsible' => [
                ['field' => 'responsible_phone', 'value' => $this->responsible->phone],
                ['field' => 'responsible_cpf', 'value' => $this->responsible->cpf],
                ['field' => 'responsible_date_of_birth', 'value' => $this->responsible->date_of_birth],
                ['field' => 'responsible_name', 'value' => $this->responsible->name],

            ],
            'relationType' => 'MOTHER',
        ];
    }

    public function test_fills_summoned_at_field_correctly_according_to_status(): void
    {
        $prereg = PreRegistrationFactory::new()->create();
        expect($prereg->summoned_at)->toBeNull();

        // Convoca
        $prereg->status = PreRegistration::STATUS_SUMMONED;
        $prereg->save();
        $firstSummonedAt = $prereg->fresh()->summoned_at;
        expect($firstSummonedAt)->not->toBeNull();
        expect($firstSummonedAt)->toBeInstanceOf(\Carbon\Carbon::class);
        expect($firstSummonedAt->isToday())->toBeTrue();

        // Indeferir
        $prereg->status = PreRegistration::STATUS_REJECTED;
        $prereg->save();
        expect($prereg->fresh()->summoned_at->timestamp)->toEqual($firstSummonedAt->timestamp);

        // Aguarda 1 segundo para garantir timestamp diferente
        sleep(1);

        // Convocar novamente
        $prereg->status = PreRegistration::STATUS_SUMMONED;
        $prereg->save();
        $secondSummonedAt = $prereg->fresh()->summoned_at;
        expect($secondSummonedAt)->not->toBeNull();
        expect($secondSummonedAt->timestamp)->not->toEqual($firstSummonedAt->timestamp);
        expect($secondSummonedAt->isToday())->toBeTrue();
        // Testa atualização em massa
        $prereg->status = PreRegistration::STATUS_WAITING;
        $prereg->save();
        PreRegistration::where('id', $prereg->id)
            ->update(['status' => PreRegistration::STATUS_SUMMONED]);
        $massUpdateSummonedAt = $prereg->fresh()->summoned_at;
        expect($massUpdateSummonedAt)->not->toBeNull();
        expect($massUpdateSummonedAt->isToday())->toBeTrue();
    }

    public function test_respects_manual_summoned_at_and_updates_when_not_manual(): void
    {
        $prereg = PreRegistrationFactory::new()->create();
        $manualDate = now()->subDays(5);

        // Define summoned_at manualmente e muda status
        $prereg->summoned_at = $manualDate;
        $prereg->status = PreRegistration::STATUS_SUMMONED;
        $prereg->save();

        // Deve manter a data manual
        expect($prereg->fresh()->summoned_at->timestamp)->toEqual($manualDate->timestamp);

        // Muda status sem definir summoned_at manualmente
        $prereg->status = PreRegistration::STATUS_WAITING;
        $prereg->save();
        sleep(1); // Garante timestamp diferente
        $prereg->status = PreRegistration::STATUS_SUMMONED;
        $prereg->save();

        // Deve atualizar para nova data
        $newSummonedAt = $prereg->fresh()->summoned_at;
        expect($newSummonedAt->timestamp)->not->toEqual($manualDate->timestamp);
        expect($newSummonedAt->isToday())->toBeTrue();
    }
}
