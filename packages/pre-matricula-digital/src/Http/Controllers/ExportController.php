<?php

namespace iEducar\Packages\PreMatricula\Http\Controllers;

use iEducar\Packages\PreMatricula\Models\PreRegistration;
use iEducar\Packages\PreMatricula\Models\PreRegistrationExporter;
use iEducar\Packages\PreMatricula\Models\ProcessField;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

/**
 * @codeCoverageIgnore
 */
class ExportController
{
    /**
     * @param  string  $type
     * @return int|null
     */
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

    /**
     * @param  string  $status
     * @return int|null
     */
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

    /**
     * @return Response
     *
     * @throws CannotInsertRecord
     */
    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $customFieldsColumns = [];
        $fields = [];

        $query = PreRegistrationExporter::query()->with('fields');

        if ($year = $request->query('year')) {
            $query->whereHas('process', function ($query) use ($year) {
                $query->where('school_year_id', $year);
            });
        }

        if ($type = $request->query('type')) {
            $query->where('preregistration_type_id', $this->type($type));
        }

        if ($status = $request->query('status')) {
            $query->where('status', $this->status($status));
        }

        if ($process = $request->query('process')) {
            $query->where('process_id', $process);

            $fields = ProcessField::query()
                ->with('field.options')
                ->whereHas('field', function ($query) {
                    $query->whereNull('internal');
                })
                ->where('process_id', $process)
                ->orderBy('field_id')
                ->get();

            $customFieldsColumns = $fields->map(function ($field) {
                return $field->field->name;
            });
        }

        if ($processes = $request->query('processes')) {
            $query->whereIn('process_id', $processes);
        }

        if ($period = $request->query('period')) {
            $query->where('period_id', $period);
        }

        if ($school = $request->query('school')) {
            $query->where('school_id', $school);
        }

        if ($grade = $request->query('grade')) {
            $query->where('grade_id', $grade);
        }

        $csv = Writer::createFromPath(storage_path(uniqid() . '.csv'), 'w+');

        $csv->insertOne([
            'Protocolo',
            'Nome do aluno(a)',
            'Data de nascimento do(a) aluno(a)',
            'CPF do(a) aluno(a)',
            'Gênero do(a) aluno(a)',
            'Nome do responsável',
            'Telefone do responsável',
            'E-mail do responsável',
            'Endereço',
            'Número',
            'Complemento',
            'Bairro',
            'Cidade',
            'CEP',
            'Escola',
            'Série',
            'Turno',
            'Posição',
            'Data de inscrição',
            'Tipo de inscrição',
            'Situação',
            'Justificativa',
            'Inicias do aluno(a)',
            ...$customFieldsColumns,
        ]);

        $query
            ->orderBy('school_name')
            ->orderBy('grade_name')
            ->orderBy('status_name')
            ->orderBy('position')
            ->orderBy('student_name')
            ->chunk(1000, function ($collection) use ($csv, $fields) {
                $collection = $collection->map(function ($item) use ($fields) {
                    $customFields = [];

                    foreach ($fields as $processField) {
                        $customFields[$processField->field_id] = $item->fields->pluck('value', 'field_id')->get($processField->field_id);

                        if ($processField->field->hasOptions()) {
                            $customFields[$processField->field_id] = $processField->field->options->pluck('name', 'id')->get($customFields[$processField->field_id]);
                        }

                        if ($processField->field->isCheckbox()) {
                            $customFields[$processField->field_id] = $customFields[$processField->field_id] === 'true' ? 'Sim' : 'Não';
                        }
                    }

                    $data = $item->only([
                        'protocol',
                        'student_name',
                        'student_date_of_birth',
                        'student_cpf',
                        'student_gender',
                        'responsible_name',
                        'responsible_phone',
                        'responsible_email',
                        'responsible_address',
                        'responsible_number',
                        'responsible_complement',
                        'responsible_neighborhood',
                        'responsible_city',
                        'responsible_postal_code',
                        'school_name',
                        'grade_name',
                        'period_name',
                        'position',
                        'created_at',
                        'type_name',
                        'status_name',
                        'observation',
                    ]);

                    return array_merge($data, [
                        'student_initials' => collect(explode(' ', $item->student_name))
                            ->reject(function ($segment) {
                                return strlen($segment) <= 2;
                            })
                            ->map(function ($segment) {
                                return mb_strtoupper(mb_substr(trim($segment), 0, 1)) . '.';
                            })->implode(''),
                    ], $customFields);
                })->toArray();

                $csv->insertAll($collection);
            });

        return new Response($csv->toString(), 200, [
            'Content-Encoding' => 'none',
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="pre-matricula.csv"',
            'Content-Description' => 'Pré-matrícula',
        ]);
    }
}
