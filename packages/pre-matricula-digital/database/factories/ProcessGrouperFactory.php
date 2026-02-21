<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\ProcessGrouper;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessGrouperFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessGrouper::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fn () => $this->faker->colorName() . ' Grouper',
            'waiting_list_limit' => 9, // Default
        ];
    }
}
