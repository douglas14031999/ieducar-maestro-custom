<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $external_person_id
 * @property string $name
 * @property int $gender
 * @property string $date_of_birth
 * @property string $cpf
 * @property string $rg
 * @property string $birth_certificate
 * @property string $phone
 * @property string $mobile
 * @property string $email
 * @property string $address
 * @property string $number
 * @property string $complement
 * @property string $neighborhood
 * @property string $postal_code
 */
class ExternalPerson extends Model
{
    protected $table = 'pmd_external_person';

    protected $primaryKey = 'preregistration_id';

    /**
     * @codeCoverageIgnore
     */
    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => match ($value) {
                'F' => Person::GENDER_FEMALE,
                'M' => Person::GENDER_MALE,
                default => null,
            }
        );
    }
}
