<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use App\Models\LegacyPerson;

class FindLegacyPersonStudentByRg
{
    public function transform($data)
    {
        $data = (array) $data;

        return $data['rg'] ?? null;
    }

    public function find($data)
    {
        $rg = $this->transform($data);

        if (empty($rg)) {
            return null;
        }

        return LegacyPerson::query()->whereHas('individual', function ($query) use ($rg) {
            $query->where('ativo', 1);
            $query->whereHas('document', function ($query) use ($rg) {
                $query->where('rg', $rg)->whereNotNull('rg');
            });
            $query->whereHas('student', function ($query) {
                $query->where('ativo', 1);
            });
        })->first();
    }
}
