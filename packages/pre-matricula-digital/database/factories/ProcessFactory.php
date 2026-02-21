<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Field;
use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Process::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'school_year_id' => fn () => SchoolYearFactory::new()->create(),
            'name' => fn () => $this->faker->colorName() . ' School',
            'active' => true, // Default
            'message_footer' => null, // Default
            'grade_age_range_link' => null, // Default
            'force_suggested_grade' => false, // Default
            'show_priority_protocol' => false, // Default
            'allow_responsible_select_map_address' => false, // Default
            'block_incompatible_age_group' => false, // Default
            'auto_reject_by_days' => false,
            'auto_reject_days' => null,
            'selected_schools' => false, // Default
            'waiting_list_limit' => 9, // Default
            'one_per_year' => false, // Default
            'show_waiting_list' => false, // Default
            'criteria' => $this->faker->text(),
            'priority_custom' => false, // Default
            'minimum_age' => null, // Default
            'reject_type_id' => Process::NO_REJECT, // Default
        ];
    }

    public function withOpenedStage(): static
    {
        return $this->afterCreating(function (Process $process) {
            ProcessStageFactory::new()->create([
                'process_id' => $process,
            ]);
        });
    }

    public function withClassroom(): static
    {
        return $this->afterCreating(function (Process $process) {
            $classroom = ClassroomFactory::new()->create([
                'school_year_id' => $process->school_year_id,
            ]);

            ProcessGradeFactory::new()->create([
                'process_id' => $process,
                'grade_id' => $classroom->grade_id,
            ]);

            ProcessPeriodFactory::new()->create([
                'process_id' => $process,
                'period_id' => $classroom->period_id,
            ]);
        });
    }

    public function withRequiredFields(): static
    {
        return $this->afterCreating(function (Process $process) {
            Field::query()->required()->get()->each(fn ($field, $index) => ProcessFieldFactory::new()->create([
                'process_id' => $process,
                'field_id' => $field->id,
                'order' => $index + 1,
                'required' => $field->required,
                'weight' => 0,
            ]));
        });
    }

    public function complete(): static
    {
        return $this->withOpenedStage()->withClassroom()->withRequiredFields();
    }

    public function withField(Field $field): static
    {
        return $this->afterCreating(function (Process $process) use ($field) {
            ProcessFieldFactory::new()->create([
                'process_id' => $process->id,
                'field_id' => $field->id,
                'order' => 99,
                'required' => false,
                'weight' => 0,
            ]);
        });
    }
}
