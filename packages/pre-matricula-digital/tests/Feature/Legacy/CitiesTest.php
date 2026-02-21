<?php

use Database\Factories\CityFactory;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateLegacyProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

uses(GraphQLTestCase::class);
uses(CreateLegacyProcess::class);

test('`cities` query', function () {
    $beta = CityFactory::new()->create([
        'name' => 'Beta City',
    ]);

    $alpha = CityFactory::new()->create([
        'name' => 'Alpha City',
    ]);

    $query = '
        query cities(
            $search: String
        ) {
            cities(
                search: $search
                orderBy: [
                    {
                        column: "name"
                        order: ASC
                    }
                ]
            ) {
                data {
                    key:id
                    label:name
                    state {
                        abbreviation
                    }
                }
            }
        }
    ';

    $this->graphQL($query)->assertJson([
        'data' => [
            'cities' => [
                'data' => [
                    [
                        'key' => $alpha->id,
                        'label' => $alpha->name,
                        'state' => [
                            'abbreviation' => $alpha->state->abbreviation,
                        ],
                    ],
                    [
                        'key' => $beta->id,
                        'label' => $beta->name,
                        'state' => [
                            'abbreviation' => $beta->state->abbreviation,
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $this->graphQL($query, [
        'search' => (string) $beta->id,
    ])->assertJson([
        'data' => [
            'cities' => [
                'data' => [
                    [
                        'key' => $beta->id,
                        'label' => $beta->name,
                        'state' => [
                            'abbreviation' => $beta->state->abbreviation,
                        ],
                    ],
                ],
            ],
        ],
    ]);
});
