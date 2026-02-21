<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\SchoolYearFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SchoolYearsTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test(): void
    {
        $schoolYear = SchoolYearFactory::new()->create();

        $query = '
            query schoolYears {
                schoolYears {
                    data {
                        id
                        year
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'schoolYears' => [
                    'data' => [
                        [
                            'id' => $schoolYear->id,
                            'year' => $schoolYear->year,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_school_year(): void
    {
        $schoolYear = SchoolYearFactory::new()->create();

        $query = '
            query schoolYear($id: ID!) {
                schoolYear(id: $id) {
                    id
                    year
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $schoolYear->id,
        ])->assertJson([
            'data' => [
                'schoolYear' => [
                    'id' => $schoolYear->id,
                    'year' => $schoolYear->year,
                ],
            ],
        ]);
    }
}
