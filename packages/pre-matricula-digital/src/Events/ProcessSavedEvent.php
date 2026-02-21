<?php

namespace iEducar\Packages\PreMatricula\Events;

use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Foundation\Events\Dispatchable;

class ProcessSavedEvent
{
    use Dispatchable;

    public function __construct(
        public Process $process,
    ) {}
}
