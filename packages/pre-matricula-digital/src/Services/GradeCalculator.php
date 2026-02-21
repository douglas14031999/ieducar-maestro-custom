<?php

namespace iEducar\Packages\PreMatricula\Services;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use iEducar\Packages\PreMatricula\Models\ProcessGradeSuggest;
use Illuminate\Database\Eloquent\Collection;

/**
 * @codeCoverageIgnore
 */
class GradeCalculator
{
    public function recalculateForProcess(Process $process): void
    {
        PreRegistration::query()
            ->with([
                'student',
            ])
            ->waiting()
            ->where('process_id', $process->getKey())
            ->where('preregistration_type_id', PreRegistration::WAITING_LIST)
            ->orderBy('created_at')
            ->chunkById(100, function (Collection $preregistrations) use ($process) {
                $preregistrations->each(function (PreRegistration $preregistration) use ($process) {
                    $this->recalculateGrade($process, $preregistration);
                });
            });
    }

    public function recalculateGrade(Process $process, PreRegistration $preregistration): void
    {
        $newGrade = ProcessGradeSuggest::query()
            ->where('process_id', $process->getKey())
            ->where('start_birth', '<=', $preregistration->student->date_of_birth)
            ->where('end_birth', '>=', $preregistration->student->date_of_birth)
            ->orderByDesc('start_birth')
            ->first();

        if ($newGrade) {
            if ($preregistration->grade_id === $newGrade->id) {
                return;
            }

            $preregistration->update([
                'grade_id' => $newGrade->id,
            ]);

            return;
        }

        $preregistration->update([
            'status' => PreRegistration::STATUS_REJECTED,
            'observation' => 'Indeferido: não há série subsequente disponível.',
        ]);
    }
}
