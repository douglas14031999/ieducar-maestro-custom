@extends('prematricula::reports.layout.default')

@section('title', $title)

@section('content')
    <table class="table table-default table-striped">
        <thead>
        <tr>
            <th>Candidato</th>
            <th style="text-align: center">Posição</th>
            <th>Escolas</th>
            <th style="text-align: center">Série</th>
            <th style="text-align: center">Turno</th>
            <th style="text-align: center">Dt. Solicitação</th>
            <th style="text-align: center">Situação</th>
        </tr>
        </thead>
        <tbody>

        @foreach($records as $record)
            <tr>
                <td class="uppercase">{{  mb_strtoupper($record[$showStudentShortName ? 'student_name_short' : 'student_name']) }}</td>
                <td class="text-center">{{ $record['position'] }}</td>
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
            <td colspan="7"><strong>Total de candidatos:</strong> {{ count($records) }}</td>
        </tr>
        </tfoot>
    </table>
@endsection
