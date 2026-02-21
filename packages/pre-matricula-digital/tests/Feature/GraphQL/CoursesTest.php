<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\CourseFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class CoursesTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test(): void
    {
        $course = CourseFactory::new()->create();

        $query = '
            query courses {
                courses {
                    data {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'courses' => [
                    'data' => [
                        [
                            'id' => $course->id,
                            'name' => $course->name,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
