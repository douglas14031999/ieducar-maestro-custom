<?php

namespace iEducar\Packages\PreMatricula\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class SchoolBuilder extends Builder
{
    public function processes($processes): static
    {
        return $this->whereHas('processes', function ($query) use ($processes) {
            $query->whereIn('process_id', $processes);
        });
    }
}
