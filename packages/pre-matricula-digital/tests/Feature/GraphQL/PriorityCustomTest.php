<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\PreRegistrationPosition;
use iEducar\Packages\PreMatricula\Tests\Fixtures\AddPriorityCustom;
use iEducar\Packages\PreMatricula\Tests\Fixtures\CreateSimpleProcess;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class PriorityCustomTest extends GraphQLTestCase
{
    use AddPriorityCustom;
    use CreateSimpleProcess;

    public function test_priority_custom(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');
        $min = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Min');

        $withoutPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withoutPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => 15_000,
        ]);

        $withoutPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 2,
        ]);

        $withoutPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $min->id,
        ]);

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => 10_000,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 2,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withoutPriority->id,
            'position' => 1,
        ]);

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withPriority->id,
            'position' => 2,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withoutPriority->id,
            'position' => 2,
        ]);

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withPriority->id,
            'position' => 1,
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withoutPriority->id,
            'priority' => 947_500,
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 950_000,
        ]);
    }

    public function test_priority_custom_with_zero_members(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => 10_000,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 0,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 900_000,
        ]);
    }

    public function test_priority_custom_with_point_income(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => '3.000',
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 990_000,
        ]);
    }

    public function test_priority_custom_with_point_and_comma_income(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => '3.000,00',
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 990_000,
        ]);
    }

    public function test_priority_custom_income_with_dot_problem(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $number = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withComma = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withoutComma = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $number->fields()->create([
            'field_id' => $this->income->id,
            'value' => '3000',
        ]);

        $withComma->fields()->create([
            'field_id' => $this->income->id,
            'value' => '3000.00',
        ]);

        $withoutComma->fields()->create([
            'field_id' => $this->income->id,
            'value' => '3000,00',
        ]);

        $number->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withComma->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withoutComma->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $number->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $withComma->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $withoutComma->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $number->id,
            'priority' => 990_000,
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withComma->id,
            'priority' => 990_000,
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withoutComma->id,
            'priority' => 990_000,
        ]);
    }

    public function test_priority_custom_with_zero_income(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => 0,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 1_000_000,
        ]);
    }

    public function test_priority_custom_with_null_income_value(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => null,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 1_000_000,
        ]);
    }

    public function test_priority_custom_with_null_income_field(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 3,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 1_000_000,
        ]);
    }

    public function test_negative_priority(): void
    {
        $this->createOpenedProcess();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $this->process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->income->id,
            'value' => 1_000_000,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->members->id,
            'value' => 1,
        ]);

        $withPriority->fields()->create([
            'field_id' => $this->multiplier->id,
            'value' => $max->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $this->process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistration::class, [
            'id' => $withPriority->id,
            'priority' => 0,
        ]);
    }

    public function test_new_preregistration(): void
    {
        $this->createOpenedProcess();
        $this->addWaitingListPeriod();
        $this->addPriorityCustom();

        $max = $this->multiplier->options->first(fn ($field) => $field['name'] === 'Max');

        $query = $this->getMutation();
        $variables = $this->getVariables();

        $variables['input']['responsible'][] = [
            'field' => 'field_' . $this->income->id,
            'value' => '10000',
        ];

        $variables['input']['responsible'][] = [
            'field' => 'field_' . $this->members->id,
            'value' => '4',
        ];

        $variables['input']['responsible'][] = [
            'field' => 'field_' . $this->multiplier->id,
            'value' => (string) $max->id,
        ];

        $this->graphQL($query, $variables)->assertJson([
            'data' => [
                'preregistrations' => [
                    [
                        'process' => [
                            'id' => $this->process->id,
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas(PreRegistration::class, [
            'priority' => 975_000,
        ]);
    }
}
