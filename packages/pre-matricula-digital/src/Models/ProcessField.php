<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $process_id
 * @property int $field_id
 * @property bool $required
 * @property int $order
 * @property int $weight
 * @property string $priority_field
 * @property Field $field
 * @property Process $process
 */
class ProcessField extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['process_id', 'field_id', 'required', 'order', 'weight', 'priority_field'];

    /**
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
    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
