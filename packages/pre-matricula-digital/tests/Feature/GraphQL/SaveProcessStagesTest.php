<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SaveProcessStagesTest extends GraphQLTestCase
{
    public function test(): void
    {
        $process = ProcessFactory::new()->create();

        $query = '
            mutation save(
                $id: ID!
                $stages: [ProcessStageInput!]!
            ) {
                process: saveProcessStages(
                    id: $id
                    stages: $stages
                ) {
                    id
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $process->getKey(),
            'stages' => [
                [
                    'type' => 'REGISTRATION',
                    'name' => 'Matrícula',
                    'radius' => 3000,
                    'startAt' => now()->format('Y-m-d H:i:s'),
                    'endAt' => now()->addMonth()->format('Y-m-d H:i:s'),
                    'observation' => 'Alguma observação',
                    'renewalAtSameSchool' => false,
                    'allowWaitingList' => false,
                    'allowSearch' => false,
                    'restrictionType' => 'NONE',
                ],
            ],
        ])->assertJson([
            'data' => [
                'process' => [
                    'id' => $process->getKey(),
                ],
            ],
        ]);
    }
}
