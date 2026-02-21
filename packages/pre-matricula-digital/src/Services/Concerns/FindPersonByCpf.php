<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use iEducar\Packages\PreMatricula\Models\Person;

class FindPersonByCpf
{
    public function transform($data)
    {
        $data = (array) $data;

        if (empty($data['cpf'])) {
            return [];
        }

        return [
            $data['cpf'],
            $data['school_year_id'],
        ];
    }

    public function find($data)
    {
        $data = $this->transform($data);

        if (empty($data)) {
            return null;
        }

        [$cpf, $schoolYearId] = $data;

        return Person::query()
            ->where('cpf', $cpf)
            ->whereHas('students', function ($studentQuery) use ($schoolYearId) {
                $studentQuery->whereHas('process', function ($processQuery) use ($schoolYearId) {
                    $processQuery->where('school_year_id', $schoolYearId);
                });
            })
            ->first();
    }
}
