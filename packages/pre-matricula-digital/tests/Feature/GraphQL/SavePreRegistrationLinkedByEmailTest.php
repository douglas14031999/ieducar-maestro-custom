<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Models\PreRegistrationLinkedByEmail;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SavePreRegistrationLinkedByEmailTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_link(): void
    {
        $email = 'responsible@exemple.com';

        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            mutation savePreRegistrationLinkedByEmail(
                $preregistration: ID!
                $email: String!
            ) {
                savePreRegistrationLinkedByEmail(
                    preregistration: $preregistration
                    email: $email
                ) {
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

        $this->graphQL($query, [
            'preregistration' => $this->preregistration->id,
            'email' => $email,
        ])->assertJson([
            'data' => [
                'savePreRegistrationLinkedByEmail' => [
                    'email' => $email,
                    'preregistration' => [
                        'id' => $this->preregistration->id,
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
        ]);
    }

    public function test_double_link(): void
    {
        $email = 'responsible@exemple.com';

        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            mutation savePreRegistrationLinkedByEmail(
                $preregistration: ID!
                $email: String!
            ) {
                savePreRegistrationLinkedByEmail(
                    preregistration: $preregistration
                    email: $email
                ) {
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

        $this->graphQL($query, [
            'preregistration' => $this->preregistration->id,
            'email' => $email,
        ])->assertJson([
            'data' => [
                'savePreRegistrationLinkedByEmail' => [
                    'email' => $email,
                    'preregistration' => [
                        'id' => $this->preregistration->id,
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
        ]);

        $this->graphQL($query, [
            'preregistration' => $this->preregistration->id,
            'email' => $email,
        ])->assertJson([
            'data' => [
                'savePreRegistrationLinkedByEmail' => [
                    'email' => $email,
                    'preregistration' => [
                        'id' => $this->preregistration->id,
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
        ]);

        $this->assertDatabaseCount(PreRegistrationLinkedByEmail::class, 1);
    }
}
