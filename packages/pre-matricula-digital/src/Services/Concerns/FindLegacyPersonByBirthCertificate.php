<?php

namespace iEducar\Packages\PreMatricula\Services\Concerns;

use App\Models\LegacyPerson;

class FindLegacyPersonByBirthCertificate
{
    public function transform($data)
    {
        $data = (array) $data;

        return $data['birth_certificate'] ?? null;
    }

    public function find($data)
    {
        $birthCertificate = $this->transform($data);

        if (empty($birthCertificate)) {
            return null;
        }

        return LegacyPerson::query()->whereHas('individual', function ($query) use ($birthCertificate) {
            $query->where('ativo', 1);
            $query->whereHas('document', function ($query) use ($birthCertificate) {
                $query->where('certidao_nascimento', $birthCertificate)->whereNotNull('certidao_nascimento');
            });
        })->first();
    }
}
