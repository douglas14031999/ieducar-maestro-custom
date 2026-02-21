<?php

namespace iEducar\Packages\PreMatricula\Console\Commands;

use iEducar\Packages\PreMatricula\Services\AutoRejectSummonedPreRegistrationsService;
use iEducar\Packages\PreMatricula\Support\Database\Connections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoRejectSummonedPreRegistrations extends Command
{
    use Connections;

    protected $signature = 'preregistration:auto-reject-summoned';

    protected $description = 'Automatically reject summoned pre-registrations that exceeded the deadline';

    public function handle(
        AutoRejectSummonedPreRegistrationsService $service
    ): int {
        foreach ($this->getConnections() as $connection) {
            $this->info("Iniciando processamento da conexão: {$connection}");

            DB::setDefaultConnection($connection);

            $service->rejectAllProcesses();

            $this->info("Finalizado processamento da conexão: {$connection}");
        }

        return 0;
    }
}
