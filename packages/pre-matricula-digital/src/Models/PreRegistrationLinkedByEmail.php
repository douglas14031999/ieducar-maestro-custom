<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreRegistrationLinkedByEmail extends Model
{
    protected $table = 'preregistration_linked_by_email';

    protected $fillable = ['preregistration_id', 'email'];

    public function preregistration(): BelongsTo
    {
        return $this->belongsTo(PreRegistration::class);
    }
}
