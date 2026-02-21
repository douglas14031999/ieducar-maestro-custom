<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $waiting_list_limit
 * @property Collection<int, Process> $processes
 */
class ProcessGrouper extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'process_grouper';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'waiting_list_limit',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (ProcessGrouper $grouper) {
            $ids = $grouper->processes->pluck('id')->toArray();

            Process::query()->whereIn('id', $ids)->update([
                'process_grouper_id' => null,
            ]);
        });
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }
}
