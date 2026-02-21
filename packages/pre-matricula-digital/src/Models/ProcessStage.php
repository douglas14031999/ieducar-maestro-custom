<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $process_id
 * @property int $process_stage_type_id
 * @property string $name
 * @property string $description
 * @property string $start_at
 * @property string $end_at
 * @property float $percent
 * @property bool $renewal_at_same_school
 * @property int $restriction_type
 * @property bool $allow_search
 * @property Process $process
 */
class ProcessStage extends Model
{
    public const TYPE_REGISTRATION_RENEWAL = 1;

    public const TYPE_REGISTRATION = 2;

    public const TYPE_WAITING_LIST = 3;

    public const STATUS_NOT_OPEN = 1;

    public const STATUS_OPEN = 2;

    public const STATUS_CLOSED = 3;

    public const RESTRICTION_NONE = 1;

    public const RESTRICTION_REGISTRATION_LAST_YEAR = 2;

    public const RESTRICTION_REGISTRATION_CURRENT_YEAR = 3;

    public const RESTRICTION_NO_REGISTRATION_CURRENT_YEAR = 4;

    public const RESTRICTION_NEW_STUDENT = 5;

    public const RESTRICTION_NO_REGISTRATION_PERIOD_CURRENT_YEAR = 6;

    public const RESTRICTION_PRE_REGISTRATION = 11;

    /**
     * @var array
     */
    protected $fillable = [
        'process_id',
        'process_stage_type_id',
        'name',
        'description',
        'start_at',
        'end_at',
        'percent',
        'radius',
        'observation',
        'renewal_at_same_school',
        'allow_waiting_list',
        'restriction_type',
        'allow_search',
    ];

    /**
     * @codeCoverageIgnore
     */
    public function getStatusAttribute()
    {
        return $this->status();
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

    /**
     * @codeCoverageIgnore
     */
    public function preregistrations()
    {
        return $this->hasMany(PreRegistration::class, 'process_stage_id', 'id');
    }

    /**
     * @codeCoverageIgnore
     */
    public function waitingPreregistrations()
    {
        return $this->hasMany(PreRegistration::class, 'process_stage_id', 'id')
            ->where('status', PreRegistration::STATUS_WAITING);
    }

    /**
     * @codeCoverageIgnore
     */
    public function acceptedPreregistrations()
    {
        return $this->hasMany(PreRegistration::class, 'process_stage_id', 'id')
            ->where('status', PreRegistration::STATUS_ACCEPTED);
    }

    /**
     * @codeCoverageIgnore
     */
    public function rejectedPreregistrations()
    {
        return $this->hasMany(PreRegistration::class, 'process_stage_id', 'id')
            ->where('status', PreRegistration::STATUS_REJECTED);
    }

    public function status()
    {
        $now = now();

        if ($this->start_at < $now && $this->end_at > $now) {
            return self::STATUS_OPEN;
        }

        if ($this->start_at > $now) {
            return self::STATUS_NOT_OPEN;
        }

        return self::STATUS_CLOSED; // @codeCoverageIgnore
    }

    public function isOpen(): bool
    {
        return $this->status() === ProcessStage::STATUS_OPEN;
    }

    public function isClosed(): bool
    {
        return !$this->isOpen();
    }
}
