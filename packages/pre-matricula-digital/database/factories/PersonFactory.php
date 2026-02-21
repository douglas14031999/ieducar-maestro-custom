<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'date_of_birth' => now()->subYear(),
            'cpf' => null, // Default
            'rg' => null, // Default
            'marital_status' => null, // Default
            'place_of_birth' => null, // Default
            'birth_certificate' => null, // Default
            'gender' => null, // Default
            'email' => null, // Default
            'phone' => null, // Default
            'mobile' => null, // Default
            'slug' => null, // Default
        ];
    }
}
