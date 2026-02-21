<?php

namespace iEducar\Packages\PreMatricula\Events;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class PreRegistrationStatusUpdatedEvent
{
    use Dispatchable;

    public function __construct(
        public PreRegistration $preregistration,
        public int $before,
        public int $after,
        public ?string $beforeJustification = null,
        public ?string $afterJustification = null,
        public ?Model $user = null
    ) {
        $this->user = $user ?? \Auth::user();
    }
}
