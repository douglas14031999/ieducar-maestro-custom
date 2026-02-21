<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FindPreRegistrationsByStudentNameAndDateOfBirthTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByStudentNameAndDateOfBirth(
                $name: String!
                $dateOfBirth: Date!
            ) {
                findPreRegistrationsByStudentNameAndDateOfBirth(
                    name: $name
                    dateOfBirth: $dateOfBirth
                ) {
                    id
                    student {
                        id
                        name
                        dateOfBirth
                    }
                    responsible {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'name' => $this->student->name,
            'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByStudentNameAndDateOfBirth' => [
                    [
                        'id' => $this->preregistration->getKey(),
                        'student' => [
                            'id' => $this->student->id,
                            'name' => $this->student->name,
                            'dateOfBirth' => $this->student->date_of_birth->format('Y-m-d'),
                        ],
                        'responsible' => [
                            'id' => $this->responsible->id,
                            'name' => $this->responsible->name,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
