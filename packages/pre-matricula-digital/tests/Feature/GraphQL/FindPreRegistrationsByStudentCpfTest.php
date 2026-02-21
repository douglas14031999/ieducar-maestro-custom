<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FindPreRegistrationsByStudentCpfTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByStudentCpf(
                $cpf: String!
            ) {
                findPreRegistrationsByStudentCpf(
                    cpf: $cpf
                ) {
                    id
                    student {
                        id
                        name
                    }
                    responsible {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'cpf' => $this->student->cpf,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByStudentCpf' => [
                    [
                        'id' => $this->preregistration->getKey(),
                        'student' => [
                            'id' => $this->student->id,
                            'name' => $this->student->name,
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
