<?php

namespace iEducar\Packages\PreMatricula\Database\Factories;

use iEducar\Packages\PreMatricula\Models\Timeline;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimelineFactory extends Factory
{
    protected $model = Timeline::class;

    public const TYPE_STATUS_UPDATED = 'preregistration-status-updated';

    public const TYPE_STUDENT_UPDATED = 'preregistration-student-updated';

    public const TYPE_RESPONSIBLE_UPDATED = 'preregistration-student-responsible-updated';

    public const TYPE_ADDRESS_UPDATED = 'student-responsible-address-updated';

    public const TYPE_EXTERNAL_SYSTEM_UPDATED = 'preregistration-external-system-address-updated';

    public const TYPE_UPDATED = 'preregistration-updated';

    public const STATUS_WAITING = 'waiting';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUMMONED = 'summoned';

    public const STATUS_IN_CONFIRMATION = 'in_confirmation';

    private const ALL_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_SUMMONED,
        self::STATUS_IN_CONFIRMATION,
    ];

    private function getRandomStatusExcept(string $exceptStatus): string
    {
        $availableStatuses = array_diff(self::ALL_STATUSES, [$exceptStatus]);

        return $this->faker->randomElement($availableStatuses);
    }

    public function definition(): array
    {
        $beforeStatus = $this->faker->randomElement(self::ALL_STATUSES);

        return [
            'model_id' => $this->faker->randomNumber(),
            'model_type' => 'iEducar\\Packages\\PreMatricula\\Models\\PreRegistration',
            'type' => self::TYPE_STATUS_UPDATED,
            'payload' => [
                'before' => [
                    'status' => $beforeStatus,
                ],
                'after' => [
                    'status' => $this->getRandomStatusExcept($beforeStatus),
                ],
            ],
        ];
    }

    public function studentUpdated(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => self::TYPE_STUDENT_UPDATED,
                'payload' => [
                    'before' => [
                        'name' => $this->faker->name,
                        'date_of_birth' => $this->faker->date('d/m/Y'),
                        'cpf' => $this->faker->cpf,
                        'marital_status' => $this->faker->randomElement(['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'Separado']),
                        'place_of_birth' => $this->faker->city,
                    ],
                    'after' => [
                        'name' => $this->faker->name,
                        'date_of_birth' => $this->faker->date('d/m/Y'),
                        'cpf' => $this->faker->cpf,
                        'marital_status' => $this->faker->randomElement(['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'Separado']),
                        'place_of_birth' => $this->faker->city,
                    ],
                ],
            ];
        });
    }

    public function responsibleUpdated(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => self::TYPE_RESPONSIBLE_UPDATED,
                'payload' => [
                    'before' => [
                        'name' => $this->faker->name,
                        'date_of_birth' => $this->faker->date('Y-m-d'),
                        'cpf' => $this->faker->randomNumber(11, true),
                        'rg' => $this->faker->randomNumber(7, true),
                        'marital_status' => $this->faker->randomElement(['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'Separado']),
                        'place_of_birth' => $this->faker->city,
                        'phone' => $this->faker->randomNumber(11, true),
                    ],
                    'after' => [
                        'name' => $this->faker->name,
                        'date_of_birth' => $this->faker->date('Y-m-d'),
                        'cpf' => $this->faker->randomNumber(11, true),
                        'rg' => $this->faker->randomNumber(7, true),
                        'marital_status' => $this->faker->randomElement(['Solteiro', 'Casado', 'Divorciado', 'Viúvo', 'Separado']),
                        'place_of_birth' => $this->faker->city,
                        'phone' => $this->faker->randomNumber(11, true),
                    ],
                ],
            ];
        });
    }

    public function addressUpdated(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => self::TYPE_ADDRESS_UPDATED,
                'payload' => [
                    'before' => [
                        'address' => $this->faker->streetName,
                        'number' => $this->faker->buildingNumber,
                        'complement' => $this->faker->optional()->secondaryAddress,
                        'neighborhood' => $this->faker->city,
                        'postal_code' => $this->faker->postcode,
                    ],
                    'after' => [
                        'address' => $this->faker->streetName,
                        'number' => $this->faker->buildingNumber,
                        'complement' => $this->faker->optional()->secondaryAddress,
                        'neighborhood' => $this->faker->city,
                        'postal_code' => $this->faker->postcode,
                    ],
                ],
            ];
        });
    }

    public function externalSystemUpdated(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => self::TYPE_EXTERNAL_SYSTEM_UPDATED,
                'payload' => [
                    'before' => [
                        'name' => $this->faker->name,
                        'cpf' => $this->faker->randomNumber(11, true),
                        'date_of_birth' => $this->faker->date('Y-m-d'),
                        'gender' => $this->faker->randomElement(['M', 'F']),
                        'rg' => $this->faker->randomNumber(7, true),
                        'birth_certificate' => $this->faker->randomNumber(10, true),
                        'phone' => $this->faker->randomNumber(2, true) . $this->faker->randomNumber(9, true),
                        'mobile' => $this->faker->randomNumber(2, true) . $this->faker->randomNumber(9, true),
                        'address' => $this->faker->streetName,
                        'number' => $this->faker->buildingNumber,
                        'complement' => $this->faker->optional()->secondaryAddress,
                        'neighborhood' => $this->faker->city,
                        'postal_code' => $this->faker->randomNumber(8, true),
                        'city' => $this->faker->city,
                    ],
                    'after' => [
                        'name' => $this->faker->name,
                        'cpf' => $this->faker->randomNumber(11, true),
                        'date_of_birth' => $this->faker->date('Y-m-d'),
                        'gender' => $this->faker->randomElement(['M', 'F']),
                        'rg' => $this->faker->randomNumber(7, true),
                        'birth_certificate' => $this->faker->randomNumber(10, true),
                        'phone' => $this->faker->randomNumber(2, true) . $this->faker->randomNumber(9, true),
                        'mobile' => $this->faker->randomNumber(2, true) . $this->faker->randomNumber(9, true),
                        'address' => $this->faker->streetName,
                        'number' => $this->faker->buildingNumber,
                        'complement' => $this->faker->optional()->secondaryAddress,
                        'neighborhood' => $this->faker->city,
                        'postal_code' => $this->faker->randomNumber(8, true),
                        'city' => $this->faker->city,
                    ],
                ],
            ];
        });
    }

    public function withSpecificStatus(string $beforeStatus, string $afterStatus): self
    {
        return $this->state(function (array $attributes) use ($beforeStatus, $afterStatus) {
            return [
                'type' => self::TYPE_STATUS_UPDATED,
                'payload' => [
                    'before' => [
                        'status' => $beforeStatus,
                    ],
                    'after' => [
                        'status' => $afterStatus,
                    ],
                ],
            ];
        });
    }

    public function preRegistrationUpdated(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => self::TYPE_UPDATED,
                'payload' => [
                    'before' => [
                        'grade' => $this->faker->randomElement(['1º Ano', '2º Ano', '3º Ano', '4º Ano', '5º Ano']),
                        'school' => $this->faker->company . ' Escola',
                        'period' => $this->faker->randomElement(['Matutino', 'Vespertino', 'Noturno', 'Integral']),
                    ],
                    'after' => [
                        'grade' => $this->faker->randomElement(['1º Ano', '2º Ano', '3º Ano', '4º Ano', '5º Ano']),
                        'school' => $this->faker->company . ' Escola',
                        'period' => $this->faker->randomElement(['Matutino', 'Vespertino', 'Noturno', 'Integral']),
                    ],
                ],
            ];
        });
    }
}
