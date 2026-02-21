<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\ClassroomFactory;
use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessGradeFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessPeriodFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class UpdatePreRegistrationTest extends GraphQLTestCase
{
    public function test_update_pre_registration(): void
    {
        $field = FieldFactory::new()->create();
        $process = ProcessFactory::new()->complete()->withField($field)->create();
        $preregistration = PreRegistrationFactory::new()->create([
            'process_id' => $process->getKey(),
        ]);

        $classroom = ClassroomFactory::new()->create([
            'school_year_id' => $process->school_year_id,
        ]);
        $beforeName = $preregistration->grade->name;
        $afterName = $classroom->grade->name;

        ProcessGradeFactory::new()->create([
            'process_id' => $process,
            'grade_id' => $classroom->grade_id,
        ]);

        ProcessPeriodFactory::new()->create([
            'process_id' => $process,
            'period_id' => $classroom->period_id,
        ]);

        $query = '
            mutation updatePreRegistration(
                $protocol: String!
                $grade: ID!
                $school: ID!
                $period: ID!
            ) {
                updatePreRegistration(
                    protocol: $protocol
                    grade: $grade
                    school: $school
                    period: $period
                ) {
                    protocol
                    grade {
                        id
                    }
                    school {
                        id
                    }
                    period {
                        id
                    }
                }
            }
        ';

        Event::fake([
            PreRegistrationUpdatedEvent::class,
        ]);

        $this->graphQL($query, [
            'protocol' => $preregistration->protocol,
            'grade' => $classroom->grade_id,
            'school' => $classroom->school_id,
            'period' => $classroom->period_id,
        ])->assertJson([
            'data' => [
                'updatePreRegistration' => [
                    'protocol' => $preregistration->protocol,
                    'grade' => [
                        'id' => $classroom->grade_id,
                    ],
                    'school' => [
                        'id' => $classroom->school_id,
                    ],
                    'period' => [
                        'id' => $classroom->period_id,
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $preregistration->getKey(),
            'grade_id' => $classroom->grade_id,
            'school_id' => $classroom->school_id,
            'period_id' => $classroom->period_id,
        ]);

        Event::assertDispatched(PreRegistrationUpdatedEvent::class, function ($event) use ($preregistration, $beforeName, $afterName) {
            return $event->preregistration->id === $preregistration->id
                && $event->before['grade'] === $beforeName
                && $event->after['grade'] === $afterName;
        });
    }
}
