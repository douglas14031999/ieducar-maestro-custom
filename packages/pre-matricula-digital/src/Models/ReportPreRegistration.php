<?php

namespace iEducar\Packages\PreMatricula\Models;

use iEducar\Packages\PreMatricula\Reports\Reports\PreRegistrationReport;
use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class ReportPreRegistration extends Model
{
    protected $table = 'report_preregistration';

    protected $primaryKey = 'protocol';

    protected $keyType = 'string';

    public function getPreRegistrationReportData($params = []): array
    {
        $query = $this->query();

        $query->selectRaw($this->fields($params));

        if (isset($params['ano'])) {
            $query->where('school_year_id', $params['ano']);
        }

        if (isset($params['type'])) {
            $query->where('preregistration_type_id', $this->type($params['type']));
        }

        if (isset($params['situacao'])) {
            $query->where('status', $this->status($params['situacao']));
        }

        if (isset($params['processos'])) {
            $query->whereIn('process_id', $params['processos']);
        }

        if (isset($params['turno'])) {
            $query->where('period_id', $params['turno']);
        }

        if (isset($params['escola'])) {
            $query->where('school_id', $params['escola']);
        }

        if (isset($params['series'])) {
            $query->whereIn('grade_id', $params['series']);
        }

        if ((int) $params['template'] === PreRegistrationReport::SIMPLIFIED_TEMPLATE) {
            $query->orderByRaw('school_id, grade_id, position ASC');
        }

        return $query->get()->toArray();
    }

    public function type($type)
    {
        switch ($type) {
            case 'REGISTRATION_RENEWAL':
                return PreRegistration::REGISTRATION_RENEWAL;
            case 'REGISTRATION':
                return PreRegistration::REGISTRATION;
            case 'WAITING_LIST':
                return PreRegistration::WAITING_LIST;
            default:
                return null;
        }
    }

    public function status($status)
    {
        switch ($status) {
            case 'WAITING':
                return PreRegistration::STATUS_WAITING;
            case 'ACCEPTED':
                return PreRegistration::STATUS_ACCEPTED;
            case 'REJECTED':
                return PreRegistration::STATUS_REJECTED;
            case 'SUMMONED':
                return PreRegistration::STATUS_SUMMONED;
            case 'IN_CONFIRMATION':
                return PreRegistration::STATUS_IN_CONFIRMATION;
            default:
                return null;
        }
    }

    protected function fields($params = []): string
    {
        $fields = [
            'TO_CHAR(ROW_NUMBER() OVER(ORDER BY created_at), \'fm0000\') AS sequence',
            'coalesce(to_char(created_at, \'dd/mm/yyyy hh24:mi\'), \'\') AS preregistration_date_time',
            'coalesce(to_char(student_date_of_birth, \'dd/mm/yyyy\'), \'\') AS student_date_of_birth_formated',
            'CASE upper(btrim(period_name))
            WHEN \'VESPERTINO\' then \'Vesp\'
            WHEN \'MATUTINO\' then \'Mat\'
            WHEN \'NOTURNO\' then \'Not\'
            WHEN \'INTEGRAL\' then \'Int\'
            ELSE \'\' END as period_name_short',
        ];

        if ($params['showStudentShortName'] === 'true') {
            $fields[] = 'UPPER((SELECT string_agg(x.studant_names_parts, \'.\') as student_name_short FROM (
                        SELECT
                            LEFT(unnest(string_to_array(student_name, \' \')),1) as studant_names_parts
                            ) AS x
                        )) as student_name_short';
        } else {
            $fields[] = 'null as student_name_short';
        }

        $fields[] = '*';

        return implode(',', $fields);
    }
}
