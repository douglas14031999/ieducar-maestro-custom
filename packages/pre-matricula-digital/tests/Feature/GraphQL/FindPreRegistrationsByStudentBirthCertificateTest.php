<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FindPreRegistrationsByStudentBirthCertificateTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByStudentBirthCertificate(
                $birthCertificate: String!
            ) {
                findPreRegistrationsByStudentBirthCertificate(
                    birthCertificate: $birthCertificate
                ) {
                    id
                    student {
                        id
                        name
                        birthCertificate
                    }
                    responsible {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'birthCertificate' => $this->student->birth_certificate,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByStudentBirthCertificate' => [
                    [
                        'id' => $this->preregistration->getKey(),
                        'student' => [
                            'id' => $this->student->id,
                            'name' => $this->student->name,
                            'birthCertificate' => $this->student->birth_certificate,
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
