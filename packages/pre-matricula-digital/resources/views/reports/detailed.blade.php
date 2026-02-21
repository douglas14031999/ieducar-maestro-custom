@extends('prematricula::reports.layout.default')

@section('title', $title)

@section('content')
    <style>
        @page  {
            size: A4 landscape !important;
        }
    </style>
    <table class="table table-default table-striped">
        <thead>
        <tr>
            <th style="text-align: center">#</th>
            <th>Protocolo</th>
            <th>Candidato</th>
            <th>Dt. Nascimento</th>
            <th>Responsável</th>
            <th>Telefone</th>
            <th>Escolas</th>
            <th style="text-align: center;min-width: 50pt">Série</th>
            <th style="text-align: center">Turno</th>
            <th style="text-align: center">Dt. Solicitação</th>
            <th style="text-align: center">Situação</th>
        </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr>
                <td class="text-center">{{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $record['protocol'] }}</td>
                <td>{{  mb_strtoupper($record[$showStudentShortName ? 'student_name_short' : 'student_name']) }}</td>
                <td>{{ $record['student_date_of_birth_formated'] }}</td>
                <td>{{ mb_strtoupper($record['student_responsible_name']) }}</td>
                <td nowrap>{{ $record['responsible_phone'] }}</td>
                <td>{{ $record['school_name'] }}</td>
                <td class="text-center">{{ $record['grade_name'] }}</td>
                <td class="text-center">{{ $record['period_name_short'] }}</td>
                <td class="text-center" nowrap>{{ $record['preregistration_date_time'] }}</td>
                <td class="text-center">{{ $record['status_name'] }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="text-right" style="padding-top: 2pt">
            <td colspan="11"><strong>Total de candidatos:</strong> {{ count($records) }}</td>
        </tr>
        </tfoot>
    </table>
@endsection
