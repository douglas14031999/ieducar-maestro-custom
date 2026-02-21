<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $position
 * @property PreRegistration $preregistration
 */
class PreRegistrationPosition extends Model
{
    /**
     * @var string
     */
    protected $table = 'preregistration_position';

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function preregistration()
    {
        return $this->belongsTo(PreRegistration::class, 'id');
    }
}
