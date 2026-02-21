<?php

namespace iEducar\Packages\PreMatricula\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessVacancyTotal extends Model
{
    protected $table = 'process_vacancy_statistics';

    public function newQuery()
    {
        return parent::newQuery()
            ->selectRaw('process_id')
            ->selectRaw('sum(total) as total')
            ->selectRaw('sum(available_vacancies) as available_vacancies')
            ->selectRaw('sum(available) as available')
            ->selectRaw('sum(waiting) as waiting')
            ->selectRaw('sum(accepted) as accepted')
            ->selectRaw('sum(rejected) as rejected')
            ->selectRaw('min(available_vacancies) < 0 as exceded_vacancies')
            ->groupBy('process_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
