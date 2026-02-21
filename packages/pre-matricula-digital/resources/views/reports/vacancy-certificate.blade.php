@extends('prematricula::reports.layout.default')

@section('title', $title)

@push('css')
    <style>
        .certificate {
            margin-left: 10pt;
            margin-right: 10pt;
        }

        .certificate * {
            text-align: justify !important;
        }

        .certificate *, .certificate label {
            font-size: 10pt !important;
            line-height: 14pt !important;
        }

        .checkmark-container {
            display: block;
            position: relative;
            padding-left: 18pt;
            margin-right: 20pt;
        }
        .checkmark-container.checkmark-inline {
            display: inline-block !important;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 11pt;
            width: 13pt;
            background-color: white;
            border: 1px solid black;
        }

        .flex-row {
            display: -webkit-box
        }

        .flex-column {
            -webkit-box-flex: 1
        }

        .center {
            margin: 0 auto;
        }
    </style>
@endpush

@section('content')
    <div class="certificate">
        <div style="height: 550pt">
            <p style="margin-bottom: 30pt; text-indent: 30pt">
                {{ $certificateName }} para os devidos fins e a pedido da parte interessada, que há vaga
                na unidade {{ $school }},
                no(a) {{ $grade }}, do(s)
                {{ $course }}
                para o ano letivo de {{ $year }}, podendo ser aceita a matrícula por transferência do(a)
                aluno(a) {{ mb_strtoupper($name) }} desde que apresente a documentação exigida
            </p>
            <br>
            <p>Confirmar o turno:</p>
            <label class="checkmark-container checkmark-inline">
                <span class="checkmark"></span> Matutino
            </label>
            <label class="checkmark-container checkmark-inline">
                <span class="checkmark"></span> Vespertino
            </label>
            <label class="checkmark-container checkmark-inline">
                <span class="checkmark"></span> Noturno
            </label>
            <label class="checkmark-container checkmark-inline">
                <span class="checkmark"></span> Integral
            </label>
            <br><br>
            <p>No caso de transferência durante o ano, apresentar também:</p>
            <label class="checkmark-container">
                <span class="checkmark"></span> Notas parciais ou Parecer descritivo parcial;
            </label>
            <label class="checkmark-container">
                <span class="checkmark"></span> Boletim escolar ou Ficha individual dos bimestres concluídos.
            </label>

            @if($validate)
                <p style="text-align: center !important; margin-top: 30pt">Este documento possui data de validade de <strong>{{ $validate }} {{ $validate > 1 ? 'dias' : 'dia' }}</strong> a partir da data de sua emissão</p>
            @endif
        </div>

        <p style="text-align: right !important;">
            {{ $institution['cidade'] }}, {{ now()->translatedFormat('d \d\e F \d\e Y') }}.
        </p>

        <div class="flex-row" style="margin-top: 30pt">
            <div class="flex-column">
                <div class="center" style="width:  200pt">
                    <div style="min-height: 14pt;"></div>
                    <div style="border-top: 1pt solid black;padding-top: 10px;margin-top: 6px;text-align: center!important;">ASSINATURA</div>
                </div>
            </div>
        </div>
    </div>
@endsection
