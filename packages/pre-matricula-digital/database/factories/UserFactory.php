<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'user_type_id' => fn () => UserTypeFactory::new()->create(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => Hash::make($this->faker->password()),
        ];
    }
}
