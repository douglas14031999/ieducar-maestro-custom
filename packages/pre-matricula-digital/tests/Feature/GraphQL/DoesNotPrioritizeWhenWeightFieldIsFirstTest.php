<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFieldFactory;
use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class DoesNotPrioritizeWhenWeightFieldIsFirstTest extends GraphQLTestCase
{
    public function test(): void
    {
        $process = ProcessFactory::new()
            ->afterCreating(function (Process $process) {
                $field = FieldFactory::new()->select()->withOptionsAndWeight([
                    'Sim' => 100,
                    'Não' => 0,
                ])->create();

                ProcessFieldFactory::new()->create([
                    'process_id' => $process,
                    'field_id' => $field,
                    'order' => 1,
                    'required' => true,
                    'weight' => 1,
                ]);
            })
            ->complete()
            ->create();

        $query = '
            mutation NewPreRegistration(
                $input: PreRegistrationInput!
            ) {
                preregistrations: newPreRegistration(
                   input: $input
                ) {
                    id
                    process {
                        id
                    }
                }
            }
        ';

        $field = $process->fields->first()->field;

        $variables = [
            'input' => [
                'process' => $process->getKey(),
                'stage' => $process->stages->first()->getKey(),
                'type' => 'REGISTRATION',
                'grade' => $process->grades->first()->getKey(),
                'period' => $process->periods->first()->getKey(),
                'school' => $process->schools->first()->getKey(),
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
                        'field' => 'field_' . $field->id,
                        'value' => (string) $field->options->first()->id,
                    ],
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

        $id = $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $process->id,
                        ],
                    ],
                ],
            ],
        ])->json('data.preregistrations.*.id')[0];

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $id,
            'priority' => 100,
        ]);
    }
}
