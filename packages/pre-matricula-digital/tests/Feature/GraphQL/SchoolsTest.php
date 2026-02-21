<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ClassroomFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\SchoolFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SchoolsTest extends GraphQLTestCase
{
    protected bool $logged = false;

    public function test(): void
    {
        $school = SchoolFactory::new()->create();

        $query = '
            query schools {
                schools {
                    data {
                        id
                        name
                        latitude
                        longitude
                        area_code
                        phone
                        email
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'schools' => [
                    'data' => [
                        [
                            'id' => $school->id,
                            'name' => $school->name,
                            'latitude' => $school->latitude,
                            'longitude' => $school->longitude,
                            'area_code' => $school->area_code,
                            'phone' => $school->phone,
                            'email' => $school->email,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_school(): void
    {
        $school = SchoolFactory::new()->create();

        $query = '
            query school($id: ID!) {
                school(id: $id) {
                    id
                    name
                    latitude
                    longitude
                    area_code
                    phone
                    email
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $school->id,
        ])->assertJson([
            'data' => [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'latitude' => $school->latitude,
                    'longitude' => $school->longitude,
                    'area_code' => $school->area_code,
                    'phone' => $school->phone,
                    'email' => $school->email,
                ],
            ],
        ]);
    }

    public function test_get_grouped_vacancies(): void
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
            query getGroupedVacancies(
                $school: ID!
                $schoolYear: ID!
                $grades: [ID!]!
                $periods: [ID!]!
            ) {
                getGroupedVacancies(
                    school: $school
                    schoolYear: $schoolYear
                    grades: $grades
                    periods: $periods
                ) {
                    grade {
                        id
                        name
                    }
                    period {
                        id
                        name
                    }
                    school {
                        id
                        name
                    }
                    vacancies
                }
            }
        ';

        $schoolYear = $process->schoolYear;
        $school = $process->schools->first();
        $grade = $process->grades->first();
        $period = $process->periods->first();

        $this->graphQL($query, [
            'school' => $school->id,
            'schoolYear' => $schoolYear->id,
            'grades' => [$grade->id],
            'periods' => [$period->id],
        ])->assertJson([
            'data' => [
                'getGroupedVacancies' => [
                    [
                        'grade' => [
                            'id' => $grade->id,
                            'name' => $grade->name,
                        ],
                        'period' => [
                            'id' => $period->id,
                            'name' => $period->name,
                        ],
                        'school' => [
                            'id' => $school->id,
                            'name' => $school->name,
                        ],
                        'vacancies' => 1,
                    ],
                ],
            ],
        ]);
    }

    public function test_schools_by_processes(): void
    {
        // Uma turma e escola que não deverão ser retornadas
        ClassroomFactory::new()->create();

        $process = ProcessFactory::new()->complete()->create();

        $school = $process->schools->first();

        $query = '
            query schools($processes: [ID!]) {
                schools(processes: $processes) {
                    data {
                        id
                        name
                        latitude
                        longitude
                        area_code
                        phone
                        email
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'processes' => [$process->id],
        ])->assertJson([
            'data' => [
                'schools' => [
                    'data' => [
                        [
                            'id' => $school->id,
                            'name' => $school->name,
                            'latitude' => $school->latitude,
                            'longitude' => $school->longitude,
                            'area_code' => $school->area_code,
                            'phone' => $school->phone,
                            'email' => $school->email,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
