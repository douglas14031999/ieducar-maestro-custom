<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ClassroomFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class ClassroomsTest extends GraphQLTestCase
{
    public function test(): void
    {
        $classroom = ClassroomFactory::new()->create();

        $query = '
            query classrooms {
                classrooms {
                    data {
                        id
                        name
                        vacancy
                        available
                        period {
                            id
                            name
                        }
                        school {
                            id
                            name
                        }
                        grade {
                            id
                            name
                        }
                        schoolYear {
                            id
                            year
                        }
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'classrooms' => [
                    'data' => [
                        [
                            'id' => $classroom->id,
                            'name' => $classroom->name,
                            'vacancy' => $classroom->vacancy,
                            'available' => $classroom->available_vacancies,
                            'period' => [
                                'id' => $classroom->period->id,
                                'name' => $classroom->period->name,
                            ],
                            'school' => [
                                'id' => $classroom->school->id,
                                'name' => $classroom->school->name,
                            ],
                            'grade' => [
                                'id' => $classroom->grade->id,
                                'name' => $classroom->grade->name,
                            ],
                            'schoolYear' => [
                                'id' => $classroom->schoolYear->id,
                                'year' => $classroom->schoolYear->year,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_classrooms_by_preregistration(): void
    {
        $preregistration = PreRegistrationFactory::new()->create();
        $classroom = $preregistration->process->schools->first()->classrooms->first();
        $classroom->update([
            'vacancy' => 2,
            'available_vacancies' => 2,
        ]);

        $query = '
            query classrooms(
                $period: ID
                $school: ID!
                $grade: ID!
            ) {
                classroomsByPreregistration(
                    period: $period
                    school: $school
                    grade: $grade
                ) {
                    data {
                        id
                        name
                        vacancy
                        available
                        period {
                            id
                            name
                        }
                        school {
                            id
                            name
                        }
                        grade {
                            id
                            name
                        }
                        schoolYear {
                            id
                            year
                        }
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'period' => $preregistration->period_id,
            'school' => $preregistration->school_id,
            'grade' => $preregistration->grade_id,
        ])->assertJson([
            'data' => [
                'classroomsByPreregistration' => [
                    'data' => [
                        [
                            'id' => $classroom->id,
                            'name' => $classroom->name,
                            'vacancy' => $classroom->vacancy,
                            'available' => $classroom->available_vacancies,
                            'period' => [
                                'id' => $classroom->period->id,
                                'name' => $classroom->period->name,
                            ],
                            'school' => [
                                'id' => $classroom->school->id,
                                'name' => $classroom->school->name,
                            ],
                            'grade' => [
                                'id' => $classroom->grade->id,
                                'name' => $classroom->grade->name,
                            ],
                            'schoolYear' => [
                                'id' => $classroom->schoolYear->id,
                                'year' => $classroom->schoolYear->year,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
