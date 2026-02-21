<?php

namespace iEducar\Packages\PreMatricula\Reports\Querys;

use App\Models\LegacyRegistration;
use BaseQueryHtml;
use iEducar\Packages\PreMatricula\Models\PreRegistration;
use Illuminate\Support\Facades\DB;

/**
 * @codeCoverageIgnore
 */
class PMDNominalByPreRegistrationQuery extends BaseQueryHtml
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
        $showStudentShortName = $this->getArg('showStudentShortName') === 'true';
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
            ->orderBy('people.cpf')
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

        return [
            'year' => $year,
            'institution' => $institution,
            'title' => 'Relatório de candidatos à pré-matrícula',
            'records' => $records->groupBy(['cpf']),
            'showStudentShortName' => $showStudentShortName,
            'disregardStudentsIeducar' => $disregardStudentsIeducar,
        ];
    }
}
