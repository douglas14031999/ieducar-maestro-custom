<?php

namespace iEducar\Packages\PreMatricula\Models\Builders;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ProcessVacancyUniqueBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        $query->selectRaw('preregistrations.process_id, count(preregistrations.id) as waiting, count(DISTINCT people.cpf) as "unique"');
        $query->join('people', 'people.id', '=', 'preregistrations.student_id');
        $query->where('status', PreRegistration::STATUS_WAITING);
        $query->groupBy('preregistrations.process_id');

        parent::__construct($query);
    }
}
