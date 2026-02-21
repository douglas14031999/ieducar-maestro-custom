<?php

namespace iEducar\Packages\PreMatricula\Models;

use iEducar\Packages\PreMatricula\Models\Builders\FieldBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $field_type_id
 * @property string $name
 * @property string $internal
 *
 * @method Field create(array $attributes)
 * @method static FieldBuilder query()
 */
class Field extends Model
{
    public const TYPE_TEXT = 1;

    public const TYPE_LONG_TEXT = 2;

    public const TYPE_SELECT = 3;

    public const TYPE_MULTI_SELECT = 4;

    public const TYPE_CHECKBOX = 5;

    public const TYPE_RADIO = 6;

    public const TYPE_CITY = 7;

    public const TYPE_DATE = 8;

    public const TYPE_TIME = 9;

    public const TYPE_EMAIL = 10;

    public const TYPE_PHONE = 11;

    public const TYPE_CPF = 12;

    public const TYPE_BIRTH_CERTIFICATE = 13;

    public const TYPE_MARITAL_STATUS = 14;

    public const TYPE_GENDER = 15;

    public const TYPE_RG = 16;

    public const TYPE_NUMBER = 18;

    public const GROUP_RESPONSIBLE = 1;

    public const GROUP_STUDENT = 2;

    public const RESPONSIBLE_NAME = 'responsible_name';

    public const RESPONSIBLE_DATE_OF_BIRTH = 'responsible_date_of_birth';

    public const RESPONSIBLE_CPF = 'responsible_cpf';

    public const RESPONSIBLE_RG = 'responsible_rg';

    public const RESPONSIBLE_GENDER = 'responsible_gender';

    public const RESPONSIBLE_MARITAL_STATUS = 'responsible_marital_status';

    public const RESPONSIBLE_PLACE_OF_BIRTH = 'responsible_place_of_birth';

    public const RESPONSIBLE_RELATION = 'responsible_relation';

    public const RESPONSIBLE_PHONE = 'responsible_phone';

    public const RESPONSIBLE_EMAIL = 'responsible_email';

    public const STUDENT_NAME = 'student_name';

    public const STUDENT_DATE_OF_BIRTH = 'student_date_of_birth';

    public const STUDENT_CPF = 'student_cpf';

    public const STUDENT_RG = 'student_rg';

    public const STUDENT_BIRTH_CERTIFICATE = 'student_birth_certificate';

    public const STUDENT_GENDER = 'student_gender';

    public const STUDENT_MARITAL_STATUS = 'student_marital_status';

    public const STUDENT_PLACE_OF_BIRTH = 'student_place_of_birth';

    public const STUDENT_EMAIL = 'student_email';

    /**
     * @var array
     */
    protected $fillable = [
        'field_type_id',
        'group_type_id',
        'name',
        'description',
        'internal',
        'required',
    ];

    public function newEloquentBuilder($query): FieldBuilder
    {
        return new FieldBuilder($query);
    }

    public function options(): HasMany
    {
        return $this->hasMany(FieldOption::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function preregistrations(): BelongsToMany
    {
        return $this->belongsToMany(PreRegistration::class, 'preregistration_fields', 'field_id', 'preregistration_id');
    }

    /**
     * @codeCoverageIgnore
     */
    public function processes()
    {
        return $this->belongsToMany(Process::class, 'process_fields')
            ->withPivot('required', 'order');
    }

    /**
     * @codeCoverageIgnore
     */
    public function isCheckbox()
    {
        return in_array($this->field_type_id, [
            self::TYPE_CHECKBOX,
        ]);
    }

    public function hasOptions()
    {
        return in_array($this->field_type_id, [
            self::TYPE_RADIO,
            self::TYPE_SELECT,
            self::TYPE_MULTI_SELECT,
        ]);
    }

    public function isDate()
    {
        return $this->field_type_id === self::TYPE_DATE;
    }
}
