<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Classroom::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'period_id' => fn () => PeriodFactory::new()->create(),
            'school_id' => fn () => SchoolFactory::new()->create(),
            'grade_id' => fn () => GradeFactory::new()->create(),
            'school_year_id' => fn () => SchoolYearFactory::new()->create(),
            'name' => $this->faker->colorName() . ' Classroom',
            'vacancy' => 1,
            'available_vacancies' => 1,
            'available' => 1,
            'multi' => false,
        ];
    }
}
