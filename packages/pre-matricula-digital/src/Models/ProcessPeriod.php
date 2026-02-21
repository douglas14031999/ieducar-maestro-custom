<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $process_id
 * @property string $period_id
 */
class ProcessPeriod extends Model
{
    protected $table = 'process_period';

    /**
     * @var array
     */
    protected $fillable = ['process_id', 'period_id'];

    /**
     * @codeCoverageIgnore
     */
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
