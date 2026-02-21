<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use iEducar\Packages\PreMatricula\Models\Person;
use Illuminate\Support\Str;

class FindPersonByNameAndDateOfBirth
{
    public function transform($data)
    {
        $data = (array) $data;

        if (empty($data['name']) || empty($data['date_of_birth'])) {
            return [];
        }

        return [
            Str::slug($data['name'], ' '),
            $data['date_of_birth'],
            $data['school_year_id'],
        ];
    }

    public function find($data)
    {
        $data = $this->transform($data);

        if (empty($data)) {
            return null;
        }

        [$name, $dateOfBirth, $schoolYearId] = $data;

        return Person::query()
            ->where('slug', 'like', $name)
            ->where('date_of_birth', $dateOfBirth)
            ->whereHas('students', function ($studentQuery) use ($schoolYearId) {
                $studentQuery->whereHas('process', function ($processQuery) use ($schoolYearId) {
                    $processQuery->where('school_year_id', $schoolYearId);
                });
            })
            ->first();
    }
}
