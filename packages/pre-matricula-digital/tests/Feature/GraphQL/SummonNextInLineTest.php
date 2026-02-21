<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class SummonNextInLineTest extends GraphQLTestCase
{
    public function test_success(): void
    {
        $preregistration = PreRegistrationFactory::new()->create();
        $preregistrationWithPriority = PreRegistrationFactory::new()->create([
            'process_id' => $preregistration->process_id,
            'priority' => 1,
        ]);

        $query = '
            query summonNextInLine(
                $process: ID!
                $school: ID!
                $grade: ID!
                $period: ID!
            ) {
                summonNextInLine(
                    process: $process
                    school: $school
                    grade: $grade
                    period: $period
                ) {
                    id
                    protocol
                }
            }
        ';

        $this->graphQL($query, [
            'process' => $preregistration->process_id,
            'school' => $preregistration->school_id,
            'grade' => $preregistration->grade_id,
            'period' => $preregistration->period_id,
        ])->assertJson([
            'data' => [
                'summonNextInLine' => [
                    'id' => $preregistrationWithPriority->getKey(),
                    'protocol' => $preregistrationWithPriority->protocol,
                ],
            ],
        ]);
    }

    public function test_without_pre_registration_in_waiting_status(): void
    {
        $preregistration = PreRegistrationFactory::new()->accepted()->create();

        $query = '
            query summonNextInLine(
                $process: ID!
                $school: ID!
                $grade: ID!
                $period: ID!
            ) {
                summonNextInLine(
                    process: $process
                    school: $school
                    grade: $grade
                    period: $period
                ) {
                    id
                    protocol
                }
            }
        ';

        $this->graphQL($query, [
            'process' => $preregistration->process_id,
            'school' => $preregistration->school_id,
            'grade' => $preregistration->grade_id,
            'period' => $preregistration->period_id,
        ])->assertJson([
            'data' => [
                'summonNextInLine' => null,
            ],
        ]);
    }

    public function test_without_pre_registration(): void
    {
        $process = ProcessFactory::new()->complete()->create();
        $period = $process->periods->first();
        $school = $process->schools->first();
        $grade = $process->grades->first();

        $query = '
            query summonNextInLine(
                $process: ID!
                $school: ID!
                $grade: ID!
                $period: ID!
            ) {
                summonNextInLine(
                    process: $process
                    school: $school
                    grade: $grade
                    period: $period
                ) {
                    id
                    protocol
                }
            }
        ';

        $this->graphQL($query, [
            'process' => $process->id,
            'school' => $school->id,
            'grade' => $grade->id,
            'period' => $period->id,
        ])->assertJson([
            'data' => [
                'summonNextInLine' => null,
            ],
        ]);
    }
}
