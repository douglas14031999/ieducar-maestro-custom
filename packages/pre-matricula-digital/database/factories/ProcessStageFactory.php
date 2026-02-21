<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\ProcessStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessStage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startAt = now()->subMonth();
        $endAt = now()->addMonth();

        return [
            'process_id' => fn () => ProcessFactory::new()->create(),
            'process_stage_type_id' => ProcessStage::TYPE_REGISTRATION,
            'name' => 'Período de matrícula',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'description' => null, // Default
            'observation' => null, // Default
            'radius' => null, // Default
            'renewal_at_same_school' => false, // Default
            'allow_waiting_list' => false, // Default
            'restriction_type' => 1, // Default
        ];
    }
}
