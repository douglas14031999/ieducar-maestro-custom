<?php

namespace iEducar\Packages\PreMatricula\Tests\Services;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFieldFactory;
use iEducar\Packages\PreMatricula\Services\PreRegistrationService;
use iEducar\Packages\PreMatricula\Services\Weight\Weigher;
use iEducar\Packages\PreMatricula\Tests\TestCase;

class PreRegistrationServiceTest extends TestCase
{
    protected PreRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PreRegistrationService(
            new Weigher
        );
    }

    public function test_when_process_field_do_not_have_weight(): void
    {
        $field = FieldFactory::new()->select()->withOptionsAndWeight([
            'Sim' => 100,
            'Não' => 0,
        ])->create();

        $option = $field->options->first();

        $processField = ProcessFieldFactory::new()->create([
            'field_id' => $field,
            'order' => 1,
            'required' => true,
            'weight' => 0,
        ]);

        $weight = $this->service->getWeightByField($processField, $option->id);

        $this->assertEquals(0, $weight);
    }

    public function test_when_process_field_has_weight(): void
    {
        $field = FieldFactory::new()->select()->withOptionsAndWeight([
            'Sim' => 100,
            'Não' => 0,
        ])->create();

        $option = $field->options->first();

        $processField = ProcessFieldFactory::new()->create([
            'field_id' => $field,
            'order' => 1,
            'required' => true,
            'weight' => 1,
        ]);

        $weight = $this->service->getWeightByField($processField, $option->id);

        $this->assertEquals(100, $weight);
    }
}
