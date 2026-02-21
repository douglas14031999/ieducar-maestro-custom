<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class CopyProcessTest extends GraphQLTestCase
{
    public function test_copy_process(): void
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
          mutation copyProcess($id: ID!) {
            process: copyProcess(id: $id) {
              name
            }
          }
        ';

        $response = $this->graphQL($query, [
            'id' => $process->getKey(),
        ]);

        $response->assertJson([
            'data' => [
                'process' => [
                    'name' => '(Cópia) ' . $process->name,
                ],
            ],
        ]);
    }
}
