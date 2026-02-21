<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\PreRegistrationLinkedByEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreRegistrationLinkedByEmailFactory extends Factory
{
    protected $model = PreRegistrationLinkedByEmail::class;

    public function definition(): array
    {
        return [
            'preregistration_id' => fn () => PreRegistrationFactory::new()->create(),
            'email' => $this->faker->email(),
        ];
    }
}
