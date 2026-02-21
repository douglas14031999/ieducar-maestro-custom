<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PreRegistrationFactory extends Factory
{
    protected $model = PreRegistration::class;

    public function definition(): array
    {
        return [
            'preregistration_type_id' => PreRegistration::REGISTRATION,
            'process_id' => fn () => ProcessFactory::new()->complete()->create(),
            'process_stage_id' => fn (array $attributes) => $this->getProcess($attributes)->stages->first(),
            'period_id' => fn (array $attributes) => $this->getProcess($attributes)->periods->first(),
            'school_id' => fn (array $attributes) => $this->getProcess($attributes)->schools->first(),
            'grade_id' => fn (array $attributes) => $this->getProcess($attributes)->grades->first(),
            'student_id' => fn () => PersonFactory::new()->create(),
            'responsible_id' => fn () => PersonFactory::new()->create(),
            'relation_type_id' => PreRegistration::RELATION_MOTHER,
            'status' => PreRegistration::STATUS_WAITING,
            'protocol' => $protocol = Str::random(6),
            'code' => md5($protocol),
            'priority' => 0,
            'parent_id' => null, // Default
            'observation' => null, // Default
            'external_person_id' => null, // Default
        ];
    }

    public function inConfirmation(): static
    {
        return $this->state([
            'status' => PreRegistration::STATUS_IN_CONFIRMATION,
        ]);
    }

    public function accepted(): static
    {
        return $this->state([
            'status' => PreRegistration::STATUS_ACCEPTED,
        ]);
    }

    private function getProcess(array $attributes): Process
    {
        return Process::query()->find($attributes['process_id']);
    }
}
