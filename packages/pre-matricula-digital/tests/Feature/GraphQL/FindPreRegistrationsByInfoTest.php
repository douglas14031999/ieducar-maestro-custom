<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PersonFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class FindPreRegistrationsByInfoTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_find_pre_registrations_by_responsible_email(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByInfo(
                $email: String
            ) {
                findPreRegistrationsByInfo(
                    email: $email
                ) {
                    id
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
        ';

        $this->graphQL($query, [
            'email' => '',
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);

        $this->responsible->update([
            'email' => 'responsible@exemple.com',
        ]);

        $this->graphQL($query, [
            'email' => $this->responsible->email,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [
                    [
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
        ]);

        $this->graphQL($query, [
            'email' => 'wrong@exemple.com',
        ])->assertExactJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);
    }

    public function test_find_pre_registrations_by_responsible_cpf(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByInfo(
                $cpf: String
            ) {
                findPreRegistrationsByInfo(
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
                        email
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'cpf' => '',
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);

        $this->responsible->update([
            'cpf' => '012.345.678-90',
        ]);

        $this->graphQL($query, [
            'cpf' => $this->responsible->cpf,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [
                    [
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
        ]);

        $this->graphQL($query, [
            'cpf' => '098.765.432-10',
        ])->assertExactJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);
    }

    public function test_find_pre_registrations_by_protocol(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $query = '
            query findPreRegistrationsByInfo(
                $protocol: String
            ) {
                findPreRegistrationsByInfo(
                    protocol: $protocol
                ) {
                    id
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
        ';

        $this->graphQL($query, [
            'protocol' => '',
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);

        $this->graphQL($query, [
            'protocol' => $this->preregistration->protocol,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [
                    [
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
        ]);

        $this->graphQL($query, [
            'protocol' => 'ABCDEF',
        ])->assertExactJson([
            'data' => [
                'findPreRegistrationsByInfo' => [],
            ],
        ]);
    }

    public function test_find_pre_registrations_by_all_info(): void
    {
        $this->createOpenedProcess();
        $this->createPreRegistration();

        $byCpf = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'responsible_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $byEmail = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
            'responsible_id' => PersonFactory::new()->create([
                'cpf' => '012.345.678-90',
            ]),
        ]);

        $byProtocol = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->getKey(),
        ]);

        $query = '
            query findPreRegistrationsByInfo(
                $cpf: String
                $email: String
                $protocol: String
            ) {
                findPreRegistrationsByInfo(
                    cpf: $cpf
                    email: $email
                    protocol: $protocol
                ) {
                    id
                    protocol
                    student {
                        id
                        name
                    }
                    responsible {
                        id
                        name
                        cpf
                        email
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'cpf' => $byCpf->responsible->cpf,
            'email' => $byEmail->responsible->email,
            'protocol' => $byProtocol->protocol,
        ])->assertJson([
            'data' => [
                'findPreRegistrationsByInfo' => [
                    [
                        'id' => $byCpf->getKey(),
                        'protocol' => $byCpf->protocol,
                        'student' => [
                            'id' => $byCpf->student->id,
                            'name' => $byCpf->student->name,
                        ],
                        'responsible' => [
                            'id' => $byCpf->responsible->id,
                            'name' => $byCpf->responsible->name,
                            'cpf' => $byCpf->responsible->cpf,
                            'email' => $byCpf->responsible->email,
                        ],
                    ],
                    [
                        'id' => $byEmail->getKey(),
                        'protocol' => $byEmail->protocol,
                        'student' => [
                            'id' => $byEmail->student->id,
                            'name' => $byEmail->student->name,
                        ],
                        'responsible' => [
                            'id' => $byEmail->responsible->id,
                            'name' => $byEmail->responsible->name,
                            'cpf' => $byEmail->responsible->cpf,
                            'email' => $byEmail->responsible->email,
                        ],
                    ],
                    [
                        'id' => $byProtocol->getKey(),
                        'protocol' => $byProtocol->protocol,
                        'student' => [
                            'id' => $byProtocol->student->id,
                            'name' => $byProtocol->student->name,
                        ],
                        'responsible' => [
                            'id' => $byProtocol->responsible->id,
                            'name' => $byProtocol->responsible->name,
                            'cpf' => $byProtocol->responsible->cpf,
                            'email' => $byProtocol->responsible->email,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseCount(PreRegistration::class, 4);
    }
}
