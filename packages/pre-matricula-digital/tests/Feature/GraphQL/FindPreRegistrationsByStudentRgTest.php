<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FindPreRegistrationsByStudentRgTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByStudentRg(
                $rg: String!
            ) {
                findPreRegistrationsByStudentRg(
                    rg: $rg
                ) {
                    id
                    student {
                        id
                        name
                        rg
                    }
                    responsible {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'rg' => $this->student->rg,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByStudentRg' => [
                    [
                        'id' => $this->preregistration->getKey(),
                        'student' => [
                            'id' => $this->student->id,
                            'name' => $this->student->name,
                            'rg' => $this->student->rg,
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
