<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $date_of_birth
 * @property string $cpf
 * @property string $rg
 * @property int $marital_status
 * @property int $place_of_birth
 * @property string $birth_certificate
 * @property int $gender
 * @property string $email
 * @property string $phone
 * @property string $mobile
 */
class Person extends Model
{
    public const GENDER_FEMALE = 1;

    public const GENDER_MALE = 2;

    protected $fillable = [
        'name',
        'date_of_birth',
        'cpf',
        'rg',
        'marital_status',
        'place_of_birth',
        'birth_certificate',
        'gender',
        'email',
        'phone',
        'mobile',
        'slug',
        'external_person_id',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            $model->slug = Str::lower(Str::slug($model->name, ' '));
        });
    }

    /**
     * @codeCoverageIgnore
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->reject(function ($namePart) {
                return strlen($namePart) <= 2;
            })
            ->map(function ($namePart) {
                return mb_substr(trim($namePart), 0, 1) . '.';
            })->implode('');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCityOfBirthAttribute(): ?string
    {
        $city = City::query()->with('state')->find($this->place_of_birth);

        if (empty($city)) {
            return null;
        }

        return $city->name . '/' . $city->state->abbreviation;
    }

    /**
     * @codeCoverageIgnore
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(PersonAddress::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function students(): HasMany
    {
        return $this->hasMany(PreRegistration::class, 'student_id');
    }
}
