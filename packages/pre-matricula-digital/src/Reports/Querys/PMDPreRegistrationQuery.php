<?php

namespace iEducar\Packages\PreMatricula\Reports\Querys;

use BaseQueryHtml;
use iEducar\Packages\PreMatricula\Models\ReportPreRegistration;

/**
 * @codeCoverageIgnore
 */
class PMDPreRegistrationQuery extends BaseQueryHtml
{
    public function get(): array
    {
        $institution = $this->getArg('institution');
        $year = $this->getArg('ano');

        $records = (new ReportPreRegistration)->getPreRegistrationReportData($this->args);

        return [
            'year' => $year,
            'institution' => $institution,
            'title' => 'Relatório de candidatos à pré-matrícula',
            'records' => $records,
            'showStudentShortName' => $this->getArg('showStudentShortName') === 'true',
        ];
    }
}
