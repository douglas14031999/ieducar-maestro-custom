<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\GradeFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class GradesTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test(): void
    {
        $grade = GradeFactory::new()->create();

        $query = '
            query grades {
                grades {
                    data {
                        id
                        name
                        course {
                            id
                            name
                        }
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'grades' => [
                    'data' => [
                        [
                            'id' => $grade->id,
                            'name' => $grade->name,
                            'course' => [
                                'id' => $grade->course->id,
                                'name' => $grade->course->name,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
