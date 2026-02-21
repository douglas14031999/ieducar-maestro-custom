<?php

namespace iEducar\Packages\PreMatricula\Tests\Feature\GraphQL;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\PreRegistrationFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFieldFactory;
use iEducar\Packages\PreMatricula\Models\PreRegistrationPosition;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Tests\GraphQLTestCase;

class RecalculateWeightTest extends GraphQLTestCase
{
    public function test(): void
    {
        $process = ProcessFactory::new()
            ->afterCreating(function (Process $process) {
                $field = FieldFactory::new()->select()->withOptionsAndWeight([
                    'Sim' => 100,
                    'Não' => 0,
                ])->create();

                ProcessFieldFactory::new()->create([
                    'process_id' => $process,
                    'field_id' => $field,
                    'order' => 1,
                    'required' => true,
                    'weight' => 1,
                ]);
            })
            ->complete()
            ->create();

        $field = $process->fields->first()->field;

        $withoutPriority = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
        ]);

        $withPriority = PreRegistrationFactory::new()->create([
            'process_id' => $process->id,
        ]);

        $withPriority->fields()->create([
            'field_id' => $field->id,
            'value' => $field->options->first()->id,
        ]);

        $this->artisan('process:recalculate-priority', [
            'process' => $process->getKey(),
        ])->expectsOutput('Priorities was recalculated for process');

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withoutPriority->id,
            'position' => 2,
        ]);

        $this->assertDatabaseHas(PreRegistrationPosition::class, [
            'preregistration_id' => $withPriority->id,
            'position' => 1,
        ]);
    }
}
