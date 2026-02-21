@php use iEducar\Packages\PreMatricula\Models\PreRegistration; @endphp
@extends('prematricula::reports.layout.default')

@section('title', $title)

@section('content')
    <table class="table table-bordered" style=" border-bottom: none">
        <thead>
            <tr>
                <th class="text-left" style="padding: 3pt">
                    ESCOLA
                </th>
                <th>
                    INFO
                </th>
                <th>
                    %
                </th>
            </tr>
        </thead>
        <tbody>
            @php($schools = $records->groupBy('school')->sortKeys())
            @php($total = $records->count())
            @foreach($schools as $name => $protocols)
                <tr class="uppercase v-align-top">
                    <td style="padding: 2pt 3pt; width: 35%">
                        {{ $name }}
                    </td>
                    <td style="padding: 2pt 3pt; width: 15%" class="text-center">
                        @php($count = $protocols->count())
                        @php($percent = ($count * 100) / $total)
                        {{ addLeadingZero($count, 3) }} inscrições
                    </td>
                    <td style="padding: 2pt 3pt; width: 50%">
                        @php($cor = $colors[$loop->index % count($colors)])
                        @php($border = $borders[$loop->index % count($borders)])
                        <div style="width: {{ $percent === 100 ? 90 : $percent }}%; background-color: {{ $cor }}; border: 2pt solid {{ $border }}; height: 17pt; padding: 5pt 0pt 0pt 5pt; margin: 0; float: left;"></div>
                        <span style="float: left; margin: 5pt 0 0 5pt">
                            {{ number_format($percent, 2) }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>
    <table class="table table-bordered" style=" border-bottom: none">
        <thead>
            <tr>
                <th class="text-left" style="padding: 3pt">
                    SÉRIE
                </th>
                <th>
                    INFO
                </th>
                <th>
                    %
                </th>
            </tr>
        </thead>
        <tbody>
            @php($grades = $records->groupBy('grade')->sortKeys())
            @foreach($grades as $name => $protocols)
                <tr class="uppercase v-align-top">
                    <td style="padding: 2pt 3pt; width: 35%">
                        {{ $name }}
                    </td>
                    <td style="padding: 2pt 3pt; width: 15%" class="text-center">
                        @php($count = $protocols->count())
                        @php($percent = ($count * 100) / $total)
                        {{ addLeadingZero($count, 3) }} inscrições
                    </td>
                    <td style="padding: 2pt 3pt; width: 50%">
                        @php($cor = $colors[$loop->index % count($colors)])
                        @php($border = $borders[$loop->index % count($borders)])
                        <div style="width: {{ $percent === 100 ? 90 : $percent }}%; background-color: {{ $cor }}; border: 2pt solid {{ $border }}; height: 17pt; padding: 5pt 0pt 0pt 5pt; margin: 0; float: left;"></div>
                        <span style="float: left; margin: 5pt 0 0 5pt">
                            {{ number_format($percent, 2) }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br><br>
    <table class="table table-bordered" style=" border-bottom: none">
            <thead>
                <tr>
                    <th class="text-left" style="padding: 3pt">
                        SITUAÇÃO
                    </th>
                    <th>
                        INFO
                    </th>
                    <th>
                        %
                    </th>
                </tr>
            </thead>
            <tbody>
                @php($situations = $records->groupBy('status')->sortKeys())
                @foreach($situations as $situation => $protocols)
                    <tr class="uppercase v-align-top">
                        <td style="padding: 2pt 3pt; width: 35%">
                            {{ $status[$situation] }}
                        </td>
                        <td style="padding: 2pt 3pt; width: 15%" class="text-center">
                            @php($count = $protocols->count())
                            @php($percent = ($count * 100) / $total)
                            {{ addLeadingZero($count, 3) }} inscrições
                        </td>
                        <td style="padding: 2pt 3pt; width: 50%">
                            @php($cor = $colors[$loop->index % count($colors)])
                            @php($border = $borders[$loop->index % count($borders)])
                            <div style="width: {{ $percent === 100 ? 90 : $percent }}%; background-color: {{ $cor }}; border: 2pt solid {{ $border }}; height: 17pt; padding: 5pt 0pt 0pt 5pt; margin: 0; float: left;"></div>
                            <span style="float: left; margin: 5pt 0 0 5pt">
                                {{ number_format($percent, 2) }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    <br><br>
    <table class="table" style=" border-bottom: none">
            <tbody>
                <tr class="uppercase v-align-top">
                    <td style="padding: 2pt 3pt; width: 35%"></td>
                    <td style="padding: 2pt 3pt; width: 65%" class="text-right">
                        <strong>
                            TOTALIZADOR DE ALUNOS: {{ $records->count() }}
                        </strong>
                    </td>
                </tr>
                <tr class="uppercase v-align-top">
                    <td style="padding: 2pt 3pt; width: 35%"></td>
                    <td style="padding: 2pt 3pt;" class="text-right">
                        Alunos com várias inscrições são considerados apenas uma vez nesses totalizadores,
                        utilizando como base um agrupamento que considera o CPF e NOME cadastrado para o aluno.
                    </td>
                </tr>
            </tbody>
        </table>
@endsection
