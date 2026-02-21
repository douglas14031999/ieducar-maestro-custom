<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = School::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName() . ' School',
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'phone' => null, // Default
            'email' => null, // Default
        ];
    }
}
