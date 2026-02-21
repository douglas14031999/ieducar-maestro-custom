<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SchoolYear::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $now = now()->addYear();

        return [
            'name' => $now->year,
            'year' => $now->year,
            'start_at' => $now->month(1)->day(15),
            'end_at' => $now->month(3)->day(15),
        ];
    }
}
