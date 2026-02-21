<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\Process;
use Illuminate\Database\Eloquent\Collection;

class FindAcceptablePreRegistration
{
    /**
     * Retorna se será permitida uma inscrição por processo (true) ou apenas uma inscrição geral (false).
     */
    private function onePreRegistrationPerProcess(): bool
    {
        return !config('prematricula.only_one_preregistration_by_student');
    }

    /**
     * Indica se os campos necessários não estão presentes.
     */
    private function isInvalid(array $data): bool
    {
        $match = 0;

        if (isset($data['cpf'])) {
            $match++;
        }

        if (isset($data['birth_certificate'])) {
            $match++;
        }

        if (isset($data['rg'])) {
            $match++;
        }

        if (isset($data['date_of_birth'], $data['name'])) {
            $match++;
        }

        return $match === 0;
    }

    private function getPreRegistration(array $data): Collection
    {
        /** @var Process $process */
        $process = Process::find($data['process_id']);

        $notGrouper = empty($process->grouper);

        return PreRegistration::query()
            ->when($notGrouper && $this->onePreRegistrationPerProcess(), function ($query) use ($data) {
                $query->where('process_id', $data['process_id']);
            })
            ->when($notGrouper && $this->onePreRegistrationPerProcess() === false, function ($query) use ($process) {
                $query->where('processes.school_year_id', $process->school_year_id);
            })
            ->whereHas('student', function ($query) use ($data) {
                $query->where(function ($query) use ($data) {
                    if (isset($data['cpf'])) {
                        $query->orWhere('cpf', $data['cpf']);
                    }

                    if (isset($data['birth_certificate'])) {
                        $query->orWhere('birth_certificate', $data['birth_certificate']);
                    }

                    if (isset($data['rg'])) {
                        $query->orWhere('rg', $data['rg']);
                    }

                    if (isset($data['date_of_birth'], $data['name'])) {
                        $query->orWhere(function ($query) use ($data) {
                            $query->where('date_of_birth', $data['date_of_birth']);
                            $query->where('name', $data['name']);
                        });
                    }
                });
            })
            ->join('processes', 'preregistrations.process_id', '=', 'processes.id')
            ->get();
    }

    public function find(array $data): Collection
    {
        if ($this->isInvalid($data)) {
            return new Collection;
        }

        return $this->getPreRegistration($data);
    }
}
