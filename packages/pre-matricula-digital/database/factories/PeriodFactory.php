<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Period::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => substr($this->faker->colorName(), 0, 7) . ' Period',
        ];
    }
}
