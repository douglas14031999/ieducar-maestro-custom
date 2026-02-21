<?php

namespace iEducar\Packages\PreMatricula\Models;

use iEducar\Packages\PreMatricula\Models\Builders\ProcessVacancyUniqueBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessVacancyUnique extends Model
{
    protected $table = 'preregistrations';

    public function newEloquentBuilder($query): ProcessVacancyUniqueBuilder
    {
        return new ProcessVacancyUniqueBuilder($query);
    }

    /**
     * @codeCoverageIgnore
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }
}
