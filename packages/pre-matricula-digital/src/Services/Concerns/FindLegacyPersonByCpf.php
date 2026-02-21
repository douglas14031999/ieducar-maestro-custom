<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use App\Models\LegacyPerson;

class FindLegacyPersonByCpf
{
    public function transform($data)
    {
        $data = (array) $data;

        return $data['cpf'] ?? null;
    }

    public function find($data)
    {
        $cpf = $this->transform($data);

        if (empty($cpf)) {
            return null;
        }

        return LegacyPerson::query()->whereHas('individual', function ($query) use ($cpf) {
            $query->where('ativo', 1);
            $query->where('cpf', str_replace(['.', '-'], '', $cpf));
        })->first();
    }
}
