<?php

namespace iEducar\Packages\PreMatricula\Reports\Querys;

use BaseQueryHtml;
use iEducar\Packages\PreMatricula\Models\PreRegistration;

/**
 * @codeCoverageIgnore
 */
class PMDVacancyCertificateQuery extends BaseQueryHtml
{
    public function get(): array
    {
        $protocol = $this->getArg('protocolo');
        $institution = $this->getArg('institution');

        $preRegistration = PreRegistration::query()->where('protocol', $protocol)->first();

        if ($preRegistration->process->auto_reject_by_days && !empty($preRegistration->process->auto_reject_days) && $preRegistration->summoned_at !== null) {
            $validate = $preRegistration->process->auto_reject_days;
        } else {
            $validate = null;
        }

        return [
            'year' => $preRegistration->process->school_year_id,
            'institution' => $institution,
            'title' => 'Atestado de Vaga',
            'name' => $preRegistration->student->name,
            'grade' => $preRegistration->grade->name,
            'school' => $preRegistration->school->name,
            'validate' => $validate,
            'certificateName' => $institution['altera_atestado_para_declaracao'] ? 'Declaramos' : 'Atestamos',
            'course' => $preRegistration->grade->course->name,
        ];
    }
}
