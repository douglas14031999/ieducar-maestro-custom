<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationLinkedByEmailFactory;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class DeletePreRegistrationLinkedByEmailTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_delete_link(): void
    {
        $linked = PreRegistrationLinkedByEmailFactory::new()->create();

        $query = '
            mutation deletePreRegistrationLinkedByEmail(
                $id: ID!
            ) {
                deletePreRegistrationLinkedByEmail(
                    id: $id
                ) {
                    id
                    email
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $linked->id,
        ])->assertJson([
            'data' => [
                'deletePreRegistrationLinkedByEmail' => [
                    'id' => $linked->id,
                    'email' => $linked->email,
                ],
            ],
        ]);
    }
}
