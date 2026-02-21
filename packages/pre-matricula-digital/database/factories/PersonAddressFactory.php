<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\PersonAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonAddressFactory extends Factory
{
    protected $model = PersonAddress::class;

    public function definition(): array
    {
        return [
            'person_id' => fn () => PersonFactory::new()->create(),
            'address' => $this->faker->streetAddress(),
            'number' => $this->faker->buildingNumber(),
            'complement' => null,
            'neighborhood' => $this->faker->colorName() . ' Village',
            'city' => $this->faker->city(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'manual_change_location' => false,
            'postal_code' => $this->faker->numerify('#####-###'),
        ];
    }
}
