<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\ProcessField;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessFieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessField::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'process_id' => fn () => ProcessFactory::new()->create(),
            'field_id' => fn () => ProcessFieldFactory::new()->create(),
            'order' => 1,
            'required' => true,
            'weight' => 0, // Default
            'priority_field' => null, // Default
        ];
    }
}
