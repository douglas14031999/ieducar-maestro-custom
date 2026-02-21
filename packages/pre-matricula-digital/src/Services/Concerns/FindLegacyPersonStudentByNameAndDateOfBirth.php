<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use App\Models\LegacyPerson;
use Illuminate\Support\Str;

class FindLegacyPersonStudentByNameAndDateOfBirth
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
        ];
    }

    public function find($data)
    {
        $data = $this->transform($data);

        if (empty($data)) {
            return null;
        }

        [$name, $dateOfBirth] = $data;

        return LegacyPerson::query()
            ->where('slug', $name)
            ->whereHas('individual', function ($query) use ($dateOfBirth) {
                $query->where('ativo', 1);
                $query->where('data_nasc', $dateOfBirth);
                $query->whereHas('student', function ($query) {
                    $query->where('ativo', 1);
                });
            })
            ->first();
    }
}
