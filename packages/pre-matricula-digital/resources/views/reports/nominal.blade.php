@php use iEducar\Packages\PreMatricula\Models\PreRegistration; @endphp
@extends('prematricula::reports.layout.default')

@section('title', $title)

@section('content')
    @foreach($records as $cpf => $protocols)
        <div class="break-avoid">
            <table class="table table-bordered" style=" border-bottom: none">
                <tr class="uppercase v-align-top">
                    <td style="padding: 2pt 3pt; border-bottom: none">
                        <b>DOCUMENTO</b>: {{ $cpf }}
                    </td>
                    <td style="padding: 2pt 3pt; border-bottom: none">
                        <b>Nº.: INSCRIÇÕES</b>: {{ $protocols->count() }}
                    </td>
                </tr>
            </table>
            <table class="table table-bordered mb-10 uppercase" style="margin-top: -1pt">
                <thead>
                <tr>
                    <th style="width: 10%">
                        PROT.
                    </th>
                    <th style="width: 22%">
                        CANDIDATO
                    </th>
                    <th style="width: 20%">
                        ESCOLA
                    </th>
                    <th style="width: 10%">
                        SÉRIE
                    </th>
                    <th style="width: 10%">
                        TURNO
                    </th>
                    <th style="width: 10%">
                        POSIÇÃO
                    </th>
                    <th style="width: 17%">
                        STATUS
                    </th>
                </tr>
                </thead>
                <tbody>
                @php
                    $protocols = $protocols->sortBy([
                        'period',
                        'position',
                    ]);
                @endphp
                @foreach($protocols as $protocol)
                    <tr>
                        <td class="text-center">
                            {{ $protocol->protocol }}
                        </td>
                        <td class="text-center">
                            @php
                                $name = $protocol->name;
                                if($showStudentShortName && !empty($name)) {
                                    $name = implode('. ', array_map(function($n) {
                                        return mb_strtoupper(mb_substr($n, 0, 1));
                                    }, array_filter(explode(' ', trim($name))))) . '.';
                                }
                            @endphp
                            {{ mb_strtoupper($name) }}
                        </td>
                        <td class="text-center">
                            {{ $protocol->school }}
                        </td>
                        <td class="text-center uppercase">
                            {{ $protocol->grade }}
                        </td>
                        <td class="text-center">
                            {{ $protocol->period }}
                        </td>
                        <td class="text-center">
                            {{ $protocol->position }}
                        </td>
                        <td class="text-center" style="vertical-align: middle !important;">
                            @php
                                $status = '';
                                $class = '';

                                switch($protocol->status) {
                                    case PreRegistration::STATUS_WAITING:
                                        $status = 'EM ESPERA';
                                        $class = 'status-yellow';
                                        break;
                                    case PreRegistration::STATUS_ACCEPTED:
                                        $status = 'DEFERIDA';
                                        $class = 'status-green';
                                        break;
                                    case PreRegistration::STATUS_REJECTED:
                                        $status = 'INDEFERIDA';
                                        $class = 'status-red';
                                        break;
                                    case PreRegistration::STATUS_SUMMONED:
                                        $status = 'CONVOCAÇÃO';
                                        $class = 'status-purple';
                                        break;
                                    case PreRegistration::STATUS_IN_CONFIRMATION:
                                        $status = 'CONFIRMAÇÃO';
                                        $class = 'status-green';
                                        break;
                                }
                            @endphp
                            <div style="width: 85%; margin: 0 auto">
                                <span class="{{ $class }}" style="float: left"></span>
                                <span style="float: left; margin: 3pt 0 0 3pt">
                                        {{ $status }}
                                    </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
    @if($disregardStudentsIeducar)
        <table class="table table-bordered" style=" border-bottom: none">
            <tbody>
            <tr class="uppercase v-align-top">
                <td style="padding: 5pt;" colspan="7" class="text-right">
                    <strong style="color: #0072ff">
                        OBSERVAÇÕES
                    </strong>
                    <br><br>
                    Se algum dos CPF's listados nesse relatório possuir uma matrícula ativa, com mesmo ano e com status
                    CURSANDO no i-Educar as pré-matrículas foram desconsideradas.
                </td>
            </tr>
            </tbody>
        </table>
    @endif
@endsection
