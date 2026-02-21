<?php

namespace iEducar\Packages\PreMatricula\Tests\Fixtures;

use iEducar\Packages\PreMatricula\Database\Factories\FieldFactory;
use iEducar\Packages\PreMatricula\Database\Factories\ProcessFieldFactory;
use iEducar\Packages\PreMatricula\Models\Field;

/**
 * @mixin CreateSimpleProcess
 */
trait AddPriorityCustom
{
    protected Field $income;

    protected Field $members;

    protected Field $multiplier;

    public function addPriorityCustom(): void
    {
        $this->multiplier = FieldFactory::new()->select()->withOptionsAndWeight([
            'None' => 0,
            'Min' => 7,
            'Max' => 10,
        ])->create();

        $this->income = FieldFactory::new()->number()->create();

        $this->members = FieldFactory::new()->number()->create();

        $this->process->update([
            'priority_custom' => true,
        ]);

        ProcessFieldFactory::new()->create([
            'process_id' => $this->process,
            'field_id' => $this->multiplier,
            'order' => 1,
            'required' => true,
            'weight' => 1,
            'priority_field' => 'multiplier',
        ]);

        ProcessFieldFactory::new()->create([
            'process_id' => $this->process,
            'field_id' => $this->income,
            'order' => 1,
            'required' => true,
            'weight' => 1,
            'priority_field' => 'income',
        ]);

        ProcessFieldFactory::new()->create([
            'process_id' => $this->process,
            'field_id' => $this->members,
            'order' => 1,
            'required' => true,
            'weight' => 1,
            'priority_field' => 'members',
        ]);
    }
}
