<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\ProcessGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessGradeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessGrade::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'process_id' => fn () => ProcessFactory::new()->create(),
            'grade_id' => fn () => GradeFactory::new()->create(),
        ];
    }
}
