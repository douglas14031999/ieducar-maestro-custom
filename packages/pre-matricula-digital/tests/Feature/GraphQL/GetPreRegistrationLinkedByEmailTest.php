<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationLinkedByEmailFactory;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class GetPreRegistrationLinkedByEmailTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find_pre_registrations_by_responsible_email(): void
    {
        $email = 'responsible@exemple.com';

        $this->createOpenedProcess();
        $this->createPreRegistration();

        $linked = PreRegistrationLinkedByEmailFactory::new()->create([
            'preregistration_id' => $this->preregistration->getKey(),
            'email' => $email,
        ]);

        $query = '
            query getPreRegistrationLinkedByEmail(
                $email: String!
            ) {
                getPreRegistrationLinkedByEmail(
                    email: $email
                ) {
                    id
                    email
                    preregistration {
                        id
                        protocol
                        student {
                            id
                            name
                        }
                        responsible {
                            id
                            name
                            email
                        }
                    }
                }
            }
        ';

        $this->responsible->update([
            'email' => $email,
        ]);

        $this->graphQL($query, [
            'email' => $email,
        ])->assertJson([
            'data' => [
                'getPreRegistrationLinkedByEmail' => [
                    [
                        'id' => $linked->getKey(),
                        'email' => $this->responsible->email,
                        'preregistration' => [
                            'id' => $this->preregistration->getKey(),
                            'student' => [
                                'id' => $this->student->id,
                                'name' => $this->student->name,
                            ],
                            'responsible' => [
                                'id' => $this->responsible->id,
                                'name' => $this->responsible->name,
                                'email' => $this->responsible->email,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->graphQL($query, [
            'email' => 'wrong@exemple.com',
        ])->assertExactJson([
            'data' => [
                'getPreRegistrationLinkedByEmail' => [],
            ],
        ]);
    }
}
