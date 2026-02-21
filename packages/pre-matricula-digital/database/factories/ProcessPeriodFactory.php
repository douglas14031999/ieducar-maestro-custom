<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\ProcessPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessPeriod::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'process_id' => fn () => ProcessFactory::new()->create(),
            'period_id' => fn () => PeriodFactory::new()->create(),
        ];
    }
}
