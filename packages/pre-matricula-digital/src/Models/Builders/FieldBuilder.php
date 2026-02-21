<?php

namespace iEducar\Packages\PreMatricula\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class FieldBuilder extends Builder
{
    public function required(): static
    {
        return $this->where('required', true);
    }
}
