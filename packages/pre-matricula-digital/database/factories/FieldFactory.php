<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Field::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'field_type_id' => Field::TYPE_TEXT,
            'group_type_id' => Field::GROUP_RESPONSIBLE,
            'name' => $this->faker->colorName() . ' Field',
            'internal' => null, // default
            'required' => false, // default
        ];
    }

    public function student(): static
    {
        return $this->state([
            'group_type_id' => Field::GROUP_STUDENT,
        ]);
    }

    public function select(): static
    {
        return $this->state([
            'field_type_id' => Field::TYPE_SELECT,
        ]);
    }

    public function number(): static
    {
        return $this->state([
            'field_type_id' => Field::TYPE_NUMBER,
        ]);
    }

    public function withOptionsAndWeight(array $options): static
    {
        return $this->afterCreating(function (Field $field) use ($options) {
            foreach ($options as $option => $weight) {
                $field->options()->create([
                    'name' => $option,
                    'weight' => $weight,
                ]);
            }
        });
    }

    public function withCustomPriority(): static
    {
        return $this;
    }
}
