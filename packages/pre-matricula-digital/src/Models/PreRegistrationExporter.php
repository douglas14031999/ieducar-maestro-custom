<?php

namespace iEducar\Packages\PreMatricula\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @codeCoverageIgnore
 */
class PreRegistrationExporter extends Model
{
    protected $table = 'exporter_preregistration';

    protected $primaryKey = 'protocol';

    protected $keyType = 'string';

    public function getStudentDateOfBirthAttribute($value): string
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::createFromTimeString($value)->format('d/m/Y H:i:s');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PreRegistrationField::class, 'preregistration_id', 'id');
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
