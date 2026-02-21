<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $field_id
 * @property string $name
 * @property int $weight
 * @property Field $field
 */
class FieldOption extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['field_id', 'name', 'weight'];

    public $timestamps = false;

    /**
     * @codeCoverageIgnore
     *
     * @return BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
