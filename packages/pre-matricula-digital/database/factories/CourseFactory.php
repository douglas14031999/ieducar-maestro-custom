<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->colorName() . ' Course',
        ];
    }
}
