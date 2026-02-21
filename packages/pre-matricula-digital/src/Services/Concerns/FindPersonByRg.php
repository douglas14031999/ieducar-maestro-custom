<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use iEducar\Packages\PreMatricula\Models\Person;

class FindPersonByRg
{
    public function transform($data)
    {
        $data = (array) $data;

        if (empty($data['rg'])) {
            return [];
        }

        return [
            $data['rg'],
            $data['school_year_id'],
        ];
    }

    public function find($data)
    {
        $data = $this->transform($data);

        if (empty($data)) {
            return null;
        }

        [$rg, $schoolYearId] = $data;

        return Person::query()
            ->where('rg', $rg)
            ->whereHas('students', function ($studentQuery) use ($schoolYearId) {
                $studentQuery->whereHas('process', function ($processQuery) use ($schoolYearId) {
                    $processQuery->where('school_year_id', $schoolYearId);
                });
            })
            ->first();
    }
}
