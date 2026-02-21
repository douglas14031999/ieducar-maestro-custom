<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PeriodFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class PeriodsTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test()
    {
        $period = PeriodFactory::new()->create();

        $query = '
            query periods {
                periods {
                    data {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'periods' => [
                    'data' => [
                        [
                            'id' => $period->id,
                            'name' => $period->name,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
