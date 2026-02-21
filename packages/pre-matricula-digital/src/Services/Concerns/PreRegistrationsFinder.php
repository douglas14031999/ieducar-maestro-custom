<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Database\Eloquent\Collection;

class PreRegistrationsFinder extends Finder
{
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

        if (isset($data['protocol'])) {
            $match++;
        }

        if (isset($data['responsible_email'])) {
            $match++;
        }

        if (isset($data['responsible_cpf'])) {
            $match++;
        }

        return $match === 0;
    }

    private function isLookingByInfo($data): bool
    {
        $match = 0;

        if (isset($data['protocol'])) {
            $match++;
        }

        if (isset($data['responsible_email'])) {
            $match++;
        }

        if (isset($data['responsible_cpf'])) {
            $match++;
        }

        return $match > 0;
    }

    private function getPreRegistrations(array $data): Collection
    {
        /** @var Collection $preregistrations */
        $preregistrations = PreRegistration::query()
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
                            $query->whereDate('date_of_birth', $data['date_of_birth']);
                            $query->whereRaw('slug like ?', [$data['name']]);
                        });
                    }
                });
            })
            ->get();

        return $preregistrations;
    }

    private function getPreRegistrationsByInfo(array $data): Collection
    {
        /** @var Collection $preregistrations */
        $preregistrations = PreRegistration::query()
            ->when(isset($data['responsible_email']), function ($query) use ($data) {
                $query->orWhereHas('responsible', function ($query) use ($data) {
                    $query->where('email', $data['responsible_email']);
                });
            })
            ->when(isset($data['responsible_cpf']), function ($query) use ($data) {
                $query->orWhereHas('responsible', function ($query) use ($data) {
                    $query->where('cpf', $data['responsible_cpf']);
                });
            })
            ->when(isset($data['protocol']), function ($query) use ($data) {
                $query->orWhere('protocol', $data['protocol']);
            })
            ->get();

        return $preregistrations;
    }

    /**
     * @param  array  $data
     */
    public function find($data): Collection|array
    {
        if ($this->isInvalid($data)) {
            return [];
        }

        if ($this->isLookingByInfo($data)) {
            return $this->getPreRegistrationsByInfo($data);
        }

        return $this->getPreRegistrations($data);
    }
}
