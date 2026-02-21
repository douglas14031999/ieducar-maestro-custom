<?php

namespace iEducar\Packages\PreMatricula\Reports\Querys;

use App\Models\LegacyRegistration;
use BaseQueryHtml;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
class PMDQuantitativeByPreRegistrationQuery extends BaseQueryHtml
{
    public function get(): array
    {
        $institution = $this->getArg('institution');
        $year = $this->getArg('ano');
        $school = $this->getArg('escola');
        $processes = $this->getArg('processos');
        $grades = $this->getArg('series');
        $situation = $this->getArg('situacao');
        $period = $this->getArg('turno');
        $disregardStudentsIeducar = $this->getArg('disregardStudentsIeducar') === 'true';

        $records = PreRegistration::query()
            ->select([
                'people.name AS name',
                'people.cpf AS cpf',
                DB::raw('regexp_replace(people.cpf, \'[^0-9]\', \'\', \'g\')::bigint as cpf_format'),
                'protocol',
                'schools.name AS school',
                'grades.name AS grade',
                'periods.name AS period',
                'position',
                'status',
            ])
            ->join('public.people', 'people.id', '=', 'preregistrations.student_id')
            ->join('public.processes', 'processes.id', '=', 'preregistrations.process_id')
            ->join('public.grades', 'grades.id', '=', 'preregistrations.grade_id')
            ->join('public.schools', 'schools.id', '=', 'preregistrations.school_id')
            ->join('public.periods', 'periods.id', '=', 'preregistrations.period_id')
            ->when($year, function ($query) use ($year) {
                return $query->where('processes.school_year_id', $year);
            })
            ->when($school, function ($query) use ($school) {
                return $query->where('school_id', $school);
            })
            ->when($processes, function ($query) use ($processes) {
                return $query->whereIn('process_id', $processes);
            })
            ->when($grades, function ($query) use ($grades) {
                return $query->whereIn('grade_id', $grades);
            })
            ->when($period, function ($query) use ($period) {
                return $query->where('period_id', $period);
            })
            ->when($situation, function ($query) use ($situation) {
                return match ($situation) {
                    'REJECTED' => $query->where('status', PreRegistration::STATUS_REJECTED),
                    'ACCEPTED' => $query->where('status', PreRegistration::STATUS_ACCEPTED),
                    'SUMMONED' => $query->where('status', PreRegistration::STATUS_SUMMONED),
                    'IN_CONFIRMATION' => $query->where('status', PreRegistration::STATUS_IN_CONFIRMATION),
                    'WAITING' => $query->where('status', PreRegistration::STATUS_WAITING),
                    default => $query,
                };
            })
            ->get();

        if ($disregardStudentsIeducar) {
            $cpfs = $records->unique('cpf_format')->pluck('cpf_format');

            $registrations = LegacyRegistration::query()
                ->select([
                    'fisica.cpf AS cpf_format',
                ])
                ->join('pmieducar.aluno', 'aluno.cod_aluno', '=', 'ref_cod_aluno')
                ->join('cadastro.fisica', 'fisica.idpes', '=', 'aluno.ref_idpes')
                ->where('aprovado', 3)
                ->where('matricula.ativo', 1)
                ->where('aluno.ativo', 1)
                ->where('ano', $year)
                ->whereIn('fisica.cpf', $cpfs)
                ->get();

            $records = $records->filter(function ($item) use ($registrations) {
                return !$registrations->contains('cpf_format', $item->cpf_format);
            });
        }

        $records = $records->unique(function ($item) {
            return $item->cpf . mb_strtoupper($item->name);
        });

        $status = [
            PreRegistration::STATUS_REJECTED => 'INDEFERIDA',
            PreRegistration::STATUS_ACCEPTED => 'DEFERIDA',
            PreRegistration::STATUS_SUMMONED => 'CONVOCAÇÃO',
            PreRegistration::STATUS_IN_CONFIRMATION => 'CONFIRMAÇÃO',
            PreRegistration::STATUS_WAITING => 'EM ESPERA',
        ];

        $colors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)',
        ];
        $borders = [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)',
        ];

        return [
            'year' => $year,
            'institution' => $institution,
            'title' => 'Relatório quantitativo de pré-matrículas',
            'records' => $records,
            'status' => $status,
            'colors' => $colors,
            'borders' => $borders,
        ];
    }
}
