<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $preregistration_id
 * @property int $field_id
 * @property string $value
 * @property Field $field
 * @property PreRegistration $preregistration
 */
class PreRegistrationField extends Model
{
    /**
     * @var string
     */
    protected $table = 'preregistration_fields';

    /**
     * @var array
     */
    protected $fillable = ['preregistration_id', 'field_id', 'value'];

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function preregistration()
    {
        return $this->belongsTo(PreRegistration::class, 'preregistration_id');
    }
}
