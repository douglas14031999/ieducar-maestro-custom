<?php

namespace iEducar\Packages\PreMatricula\Services;

use Carbon\Carbon;
use iEducar\Packages\PreMatricula\Events\PreRegistrationStatusAutoRejectedEvent;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Database\Eloquent\Collection;

class AutoRejectSummonedPreRegistrationsService
{
    public function rejectAllProcesses(): void
    {
        $processes = $this->getProcessesToReject();
        foreach ($processes as $process) {
            $this->rejectForProcess($process);
        }
    }

    public function getProcessesToReject(): Collection
    {
        return Process::where('auto_reject_by_days', true)
            ->whereNotNull('auto_reject_days')
            ->where('active', true)
            ->get();
    }

    public function rejectForProcess(Process $process): void
    {
        if (!$process->active || !$process->auto_reject_by_days) {
            return;
        }

        PreRegistration::where('process_id', $process->id)
            ->where('status', PreRegistration::STATUS_SUMMONED)
            ->whereNotNull('summoned_at')
            ->whereDate('summoned_at', '<', Carbon::now()->subDays($process->auto_reject_days))
            ->orderBy('id')
            ->chunk(100, function (Collection $preRegistrations) {
                foreach ($preRegistrations as $preRegistration) {
                    $this->rejectPreRegistration($preRegistration);
                }
            });
    }

    private function rejectPreRegistration(PreRegistration $preRegistration): void
    {
        $before = $preRegistration->status;
        $beforeJustification = $preRegistration->observation;

        $justification = 'Inscrição indeferida por vencimento do prazo de convocação';

        $preRegistration->update([
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => $justification,
        ]);

        event(new PreRegistrationStatusAutoRejectedEvent(
            preRegistration: $preRegistration,
            before: $before,
            beforeJustification: $beforeJustification,
            afterJustification: $justification
        ));
    }
}
