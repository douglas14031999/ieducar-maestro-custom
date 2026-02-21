<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property string $name
 * @property string $type
 * @property array $payload
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Timeline extends Model
{
    /**
     * @var string
     */
    protected $table = 'timelines';

    /**
     * @var array
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'type',
        'payload',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'payload' => 'array',
    ];

    public const UPDATED_AT = null;

    /**
     * @return MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }
}
