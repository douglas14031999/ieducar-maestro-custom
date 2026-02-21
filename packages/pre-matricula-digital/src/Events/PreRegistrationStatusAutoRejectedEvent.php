<?php

namespace iEducar\Packages\PreMatricula\Events;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreRegistrationStatusAutoRejectedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public PreRegistration $preRegistration,
        public string $before,
        public ?string $beforeJustification,
        public ?string $afterJustification
    ) {}
}
