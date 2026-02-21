<?php

namespace iEducar\Packages\PreMatricula\Models\Builders;

use iEducar\Packages\PreMatricula\Support\OnlyNumbers;
use Illuminate\Database\Eloquent\Builder;

class RegistrationMatchBuilder extends Builder
{
    use OnlyNumbers;

    public function year(int $year): static
    {
        return $this->where('year', $year);
    }

    public function match(array $data): static
    {
        return $this->where(function ($query) use ($data) {
            $query->where(function ($query) use ($data) {
                if (isset($data['cpf'])) {
                    $query->orWhere('cpf', $this->onlyNumbers($data['cpf']));
                }

                if (isset($data['rg'])) {
                    $query->orWhere('rg', $data['rg']);
                }

                if (isset($data['birth_certificate'])) {
                    $query->orWhere('birth_certificate', $data['birth_certificate']);
                }

                $query->orWhere(function ($query) use ($data) {
                    $query->where('slug', $data['name']);
                    $query->where('date_of_birth', $data['date_of_birth']);
                });
            });
            $query->when($data['periods'], function ($query) use ($data) {
                $query->whereIn('period', $data['periods']);
            });
        });
    }
}
