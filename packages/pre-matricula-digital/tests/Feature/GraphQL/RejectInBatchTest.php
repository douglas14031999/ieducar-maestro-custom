<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class RejectInBatchTest extends GraphQLTestCase
{
    public function test_success(): void
    {
        $preregistration = PreRegistrationFactory::new()->create();

        $query = '
            mutation rejectInBatch(
                $id: ID!
                $stageId: ID!
                $justification: String
            ) {
                rejectInBatch(
                    id: $id
                    stageId: $stageId
                    justification: $justification
                )
            }
        ';

        $this->graphQL($query, [
            'id' => $preregistration->process_id,
            'stageId' => $preregistration->process_stage_id,
            'justification' => 'Rejeitado em lote',
        ])->assertJson([
            'data' => [
                'rejectInBatch' => 1,
            ],
        ]);

        $this->assertDatabaseHas('preregistrations', [
            'id' => $preregistration->getKey(),
            'observation' => "Rejeitado em lote\n",
        ]);
    }
}
