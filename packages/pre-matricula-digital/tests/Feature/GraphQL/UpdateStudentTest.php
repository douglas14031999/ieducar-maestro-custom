<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStudentUpdatedEvent;
use iEducar\Packages\PreMatricula\Models\ProcessField;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;
use Illuminate\Support\Facades\Event;

class UpdateStudentTest extends GraphQLTestCase
{
    public function test_update_student(): void
    {
        $field = FieldFactory::new()->student()->create();
        $process = ProcessFactory::new()->complete()->withField($field)->create();
        $preregistration = PreRegistrationFactory::new()->create([
            'process_id' => $process->getKey(),
        ]);

        $beforeName = $preregistration->student->name;

        $query = '
            mutation updateStudent(
                $protocol: String!
                $fields: [PreRegistrationFieldInput!]!
            ) {
                updateStudent(
                    protocol: $protocol
                    fields: $fields
                ) {
                    id
                    protocol
                }
            }
        ';

        Event::fake([
            PreRegistrationStudentUpdatedEvent::class,
        ]);

        $this->graphQL($query, [
            'protocol' => $preregistration->protocol,
            'fields' => [
                [
                    'field' => 'student_name',
                    'value' => $afterName = 'Eder Soares',
                ],
                [
                    'field' => 'student_date_of_birth',
                    'value' => '2000-01-01',
                ],
                [
                    'field' => 'field_' . $field->getKey(),
                    'value' => 'Green',
                ],
            ],
        ])->assertJson([
            'data' => [
                'updateStudent' => [
                    'id' => $preregistration->getKey(),
                    'protocol' => $preregistration->protocol,
                ],
            ],
        ]);

        $this->assertDatabaseHas('people', [
            'name' => 'Eder Soares',
            'date_of_birth' => '2000-01-01',
        ]);

        $this->assertDatabaseHas('preregistration_fields', [
            'field_id' => $field->getKey(),
            'preregistration_id' => $preregistration->getKey(),
            'value' => 'Green',
        ]);

        Event::assertDispatched(PreRegistrationStudentUpdatedEvent::class, function ($event) use ($preregistration, $beforeName, $afterName) {
            return $event->preregistration->id === $preregistration->id
                && $event->before['name'] === $beforeName
                && $event->after['name'] === $afterName;
        });
    }

    public function test_update_priority(): void
    {
        $field = FieldFactory::new()->student()->create();
        $process = ProcessFactory::new()->complete()->withField($field)->create();
        $preregistration = PreRegistrationFactory::new()->create([
            'process_id' => $process->getKey(),
        ]);

        $this->assertDatabaseHas($preregistration, [
            'id' => $preregistration->getKey(),
            'priority' => 0,
        ]);

        ProcessField::query()->where('field_id', $field->getKey())->update([
            'weight' => 12,
        ]);

        $query = '
            mutation updateStudent(
                $protocol: String!
                $fields: [PreRegistrationFieldInput!]!
            ) {
                updateStudent(
                    protocol: $protocol
                    fields: $fields
                ) {
                    id
                    protocol
                }
            }
        ';

        $this->graphQL($query, [
            'protocol' => $preregistration->protocol,
            'fields' => [
                [
                    'field' => 'student_name',
                    'value' => 'Eder Soares',
                ],
                [
                    'field' => 'student_date_of_birth',
                    'value' => '2000-01-01',
                ],
                [
                    'field' => 'field_' . $field->getKey(),
                    'value' => 'Green',
                ],
            ],
        ])->assertJson([
            'data' => [
                'updateStudent' => [
                    'id' => $preregistration->getKey(),
                    'protocol' => $preregistration->protocol,
                ],
            ],
        ]);

        $this->assertDatabaseHas($preregistration, [
            'id' => $preregistration->getKey(),
            'priority' => 12,
        ]);
    }
}
