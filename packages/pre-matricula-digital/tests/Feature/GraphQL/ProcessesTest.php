<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ClassroomFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PersonFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Models\ProcessSchoolSelected;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class ProcessesTest extends GraphQLTestCase
{
    use CreateSimpleProcess;

    public function test_get_process(): void
    {
        $this->createOpenedProcess();

        $query = '
            query process($id: ID!) {
                process(id: $id) {
                    id
                    name
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $this->process->id,
        ])->assertJson([
            'data' => [
                'process' => [
                    'id' => $this->process->id,
                    'name' => $this->process->name,
                ],
            ],
        ]);
    }

    public function test_get_process_with_selected_schools(): void
    {
        $this->createOpenedProcess();

        $classroom = ClassroomFactory::new()->create([
            'school_year_id' => $this->process->school_year_id,
            'period_id' => $this->process->periods->first(),
            'grade_id' => $this->process->grades->first(),
        ]);

        $query = '
            query process($id: ID!) {
                process(id: $id) {
                    id
                    name
                    schools {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $this->process->id,
        ])->assertJson([
            'data' => [
                'process' => [
                    'id' => $this->process->id,
                    'name' => $this->process->name,
                    'schools' => [
                        [
                            'id' => $this->school->id,
                            'name' => $this->school->name,
                        ],
                        [
                            'id' => $classroom->school->id,
                            'name' => $classroom->school->name,
                        ],
                    ],
                ],
            ],
        ]);

        $this->process->update([
            'selected_schools' => true,
        ]);

        ProcessSchoolSelected::query()->create([
            'process_id' => $this->process->getKey(),
            'school_id' => $classroom->school_id,
        ]);

        // Garante que apenas 1 escola será retornada
        $this->graphQL($query, [
            'id' => $this->process->id,
        ])->assertJson([
            'data' => [
                'process' => [
                    'id' => $this->process->id,
                    'name' => $this->process->name,
                    'schools' => [
                        [
                            'id' => $classroom->school->id,
                            'name' => $classroom->school->name,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_get_processes(): void
    {
        $this->createOpenedProcess();

        $query = '
            query processes {
                processes {
                    data {
                        id
                        name
                    }
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'processes' => [
                    'data' => [
                        [
                            'id' => $this->process->id,
                            'name' => $this->process->name,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_process_by_stage(): void
    {
        $this->createOpenedProcess();

        $query = '
            query stage($id: ID!) {
                stage: processByStage(id: $id) {
                    id
                    renewalAtSameSchool
                    allowWaitingList
                    radius
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $this->stage->id,
        ])->assertJson([
            'data' => [
                'stage' => [
                    'id' => $this->stage->id,
                    'renewalAtSameSchool' => $this->stage->renewal_at_same_school,
                    'allowWaitingList' => $this->stage->allow_waiting_list,
                    'radius' => $this->stage->radius,
                ],
            ],
        ]);
    }

    public function test_vacancies(): void
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
            query vacancies(
                $processes: [ID!]
            ) {
                vacancies(
                    processes: $processes
                ) {
                    process
                    grade
                    school
                    period
                }
            }
        ';

        $this->graphQL($query, [
            'processes' => [$process->id],
        ])->assertJson([
            'data' => [
                'vacancies' => [
                    [
                        'process' => $process->id,
                        'grade' => $process->grades->first()->id,
                        'school' => $process->schools->first()->id,
                        'period' => $process->periods->first()->id,
                    ],
                ],
            ],
        ]);
    }

    public function test_get_process_vacancy_statistics(): void
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
            query process(
                $process: ID!
                $schools: [ID!]
                $grades: [ID!]
                $periods: [ID!]
            ) {
                statistics: getProcessVacancyStatistics(
                    process: $process
                    schools: $schools
                    grades: $grades
                    periods: $periods
                ) {
                    grade
                    period
                    school
                    total
                    waiting
                    accepted
                    rejected
                    available: availableVacancies
                }
            }

        ';

        $this->graphQL($query, [
            'process' => $process->id,
            'schools' => $process->schools->pluck('id'),
            'grades' => $process->grades->pluck('id'),
            'periods' => $process->periods->pluck('id'),
        ])->assertJson([
            'data' => [
                'statistics' => [
                    [
                        'grade' => $process->grades->first()->id,
                        'period' => $process->periods->first()->id,
                        'school' => $process->schools->first()->id,
                        'total' => 1,
                        'waiting' => 0,
                        'accepted' => 0,
                        'rejected' => 0,
                        'available' => 1,
                    ],
                ],
            ],
        ]);
    }

    public function test_get_process_vacancy_total(): void
    {
        $process = ProcessFactory::new()->complete()->create();

        $query = '
            query processes(
                $schools: [ID!]
                $grades: [ID!]
                $periods: [ID!]
            ) {
                processes: getProcessVacancyTotal(
                    schools: $schools
                    grades: $grades
                    periods: $periods
                ) {
                    process {
                        id
                        name
                    }
                    total
                    available: availableVacancies
                    waiting
                    rejected
                    accepted
                    excededVacancies
                }
            }
        ';

        $this->graphQL($query, [
            'schools' => $process->schools->pluck('id'),
            'grades' => $process->grades->pluck('id'),
            'periods' => $process->periods->pluck('id'),
        ])->assertJson([
            'data' => [
                'processes' => [
                    [
                        'process' => [
                            'id' => $process->id,
                            'name' => $process->name,
                        ],
                        'total' => 1,
                        'available' => 1,
                        'waiting' => 0,
                        'rejected' => 0,
                        'accepted' => 0,
                        'excededVacancies' => 0,
                    ],
                ],
            ],
        ]);
    }

    public function test_get_process_vacancy_unique(): void
    {
        $student = PersonFactory::new()->create([
            'cpf' => '000.000.000-00',
        ]);

        $p1 = PreRegistrationFactory::new()->create([
            'student_id' => $student,
        ]);

        $p2 = PreRegistrationFactory::new()->create([
            'process_id' => $p1->process,
            'student_id' => $student,
        ]);

        $query = '
            query processes {
                processes: getProcessVacancyUnique {
                    process
                    unique
                    waiting
                }
            }
        ';

        $this->graphQL($query)->assertJson([
            'data' => [
                'processes' => [
                    [
                        'process' => $p2->process->id,
                        'unique' => 1,
                        'waiting' => 2,
                    ],
                ],
            ],
        ]);
    }
}
