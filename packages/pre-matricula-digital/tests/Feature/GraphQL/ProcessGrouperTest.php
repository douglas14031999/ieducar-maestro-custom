<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessGrouperFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class ProcessGrouperTest extends GraphQLTestCase
{
    public function test_get_process_grouper(): void
    {
        $grouper = ProcessGrouperFactory::new()->create();

        $query = '
            query groupers {
                groupers {
                    id
                    name
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'groupers' => [
                    [
                        'id' => $grouper->id,
                        'name' => $grouper->name,
                    ],
                ],
            ],
        ]);
    }

    public function test_save_process_grouper(): void
    {
        $grouper = ProcessGrouperFactory::new()->make();

        $query = '
            mutation saveProcessGrouper(
                $input: ProcessGrouperInput!
            ) {
                saveProcessGrouper(
                    input: $input
                ) {
                    name
                    waitingListLimit
                }
            }
        ';

        $this->graphQL($query, [
            'input' => [
                'name' => $grouper->name,
                'waitingListLimit' => 123,
            ],
        ])->assertJson([
            'data' => [
                'saveProcessGrouper' => [
                    'name' => $grouper->name,
                    'waitingListLimit' => 123,
                ],
            ],
        ]);
    }

    public function test_save_process_grouper_linking_processes(): void
    {
        $process = ProcessFactory::new()->create();
        $grouper = ProcessGrouperFactory::new()->make();

        $query = '
            mutation saveProcessGrouper(
                $input: ProcessGrouperInput!
            ) {
                saveProcessGrouper(
                    input: $input
                ) {
                    name
                    waitingListLimit
                    processes {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'input' => [
                'name' => $grouper->name,
                'waitingListLimit' => 123,
                'processes' => [$process->getKey()],
            ],
        ])->assertJson([
            'data' => [
                'saveProcessGrouper' => [
                    'name' => $grouper->name,
                    'waitingListLimit' => 123,
                    'processes' => [
                        [
                            'id' => $process->getKey(),
                            'name' => $process->name,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_delete_process_grouper(): void
    {
        $grouper = ProcessGrouperFactory::new()->create();

        $query = '
            mutation deleteProcessGrouper(
                $id: ID!
            ) {
                deleteProcessGrouper(
                    id: $id
                ) {
                    name
                    waitingListLimit
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $grouper->id,
        ])->assertJson([
            'data' => [
                'deleteProcessGrouper' => [
                    'name' => $grouper->name,
                ],
            ],
        ]);
    }

    public function test_delete_process_grouper_with_linked_process(): void
    {
        $grouper = ProcessGrouperFactory::new()->create();
        ProcessFactory::new()->create([
            'process_grouper_id' => $grouper->getKey(),
        ]);

        $query = '
            mutation deleteProcessGrouper(
                $id: ID!
            ) {
                deleteProcessGrouper(
                    id: $id
                ) {
                    name
                    waitingListLimit
                }
            }
        ';

        $this->assertDatabaseHas('processes', [
            'process_grouper_id' => $grouper->getKey(),
        ]);

        $this->graphQL($query, [
            'id' => $grouper->id,
        ])->assertJson([
            'data' => [
                'deleteProcessGrouper' => [
                    'name' => $grouper->name,
                ],
            ],
        ]);

        $this->assertDatabaseHas('processes', [
            'process_grouper_id' => null,
        ]);
    }
}
