<?php

namespace iEducar\Packages\PreMatricula\Console\Commands;

use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Services\GradeCalculator;
use Illuminate\Console\Command;

class ProcessRecalculateGradeCommand extends Command
{
    protected $signature = 'process:recalculate-grade {process}';

    protected $description = 'Recalculate grades the pre-registrations of the process';

    public function handle(
        GradeCalculator $calculator,
    ): int {
        $process = Process::query()->findOrFail(
            $this->argument('process')
        );

        $calculator->recalculateForProcess($process);

        $this->info('Grades was recalculated for process');

        return 0;
    }
}
