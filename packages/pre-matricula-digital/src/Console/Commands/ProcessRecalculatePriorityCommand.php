<?php

namespace iEducar\Packages\PreMatricula\Console\Commands;

use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Services\PriorityCalculator;
use Illuminate\Console\Command;

class ProcessRecalculatePriorityCommand extends Command
{
    protected $signature = 'process:recalculate-priority {process}';

    protected $description = 'Recalculate priority of the process';

    public function handle(
        PriorityCalculator $calculator,
    ): int {
        $process = Process::query()->findOrFail(
            $this->argument('process')
        );

        $calculator->recalculateForProcess($process);

        $this->info('Priorities was recalculated for process');

        return 0;
    }
}
