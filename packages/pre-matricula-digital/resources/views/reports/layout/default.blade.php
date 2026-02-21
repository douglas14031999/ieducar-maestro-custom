<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>@yield('title')</title>
        <style>
            body * {
                font-size: 10pt;
                font-family: sans-serif !important;
            }

            table { overflow: visible !important; }
            thead { display: table-header-group }
            tfoot { display: table-row-group }
            .break-avoid, tr { page-break-inside: avoid }

            .table-row-group thead {
                display: table-row-group !important;
            }

            .table-row-group>tbody>tr {
                page-break-inside: auto;
            }

            .tr-no-column>td {
                border: none !important;
            }

            p {
                margin: 0 0 3pt 0;
            }

            h1 {
                font-size: 20pt;
            }

            h1, h2, h3, h4, h5 {
                margin: 0;
            }

            @page {
                text-align: justify;
                font-family: sans-serif !important;
                size: A4 {{ $orientation ?? 'portrait' }};
                margin: 6mm 15pt 25pt 15pt;
            }

            .table {
                margin-left: auto;
                margin-right: auto;
                border-collapse: collapse;
                width: 100%;
            }

            .table-row {
                padding: 0 !important;
            }

            .table-row tr {
                page-break-inside: auto;
            }

            .table-row table {
                border: 0 solid transparent !important;
            }

            .table-row td {
                border-top: 0 solid transparent !important;
            }

            .table-row tr:last-child td {
                border-bottom: 0 solid transparent !important;
            }

            .table-row td:first-child {
                border-left: 0 solid transparent !important;
            }

            .table-row td:last-child {
                border-right: 0 solid transparent !important;
            }

            .table.table-only-bordered {
                border: 1px solid #808080;
            }

            .table.table-bordered-no-body th, .table.table-bordered-no-body tfoot td {
                border: 1px solid #808080;
            }

            .table.table-bordered, .table.table-bordered th, .table.table-bordered td {
                border: 1px solid #808080;
            }

            .table.table-bordered-no-column {
                border: 1px solid #808080;
            }

            .table.table-bordered-no-column:not(.table-text-left) td:not(.text-center), .table.table-bordered-no-column:not(.table-text-left) th:not(.text-center) {
                text-align: right;
            }

            .table.table-text-left td, .table.table-text-left th {
                text-align: left;
            }

            .table.table-bordered-no-column td:first-child,  .table.table-bordered-no-column th:first-child{
                text-align: left;
            }

            .table.table-bordered-no-column tr {
                border-top: 1px solid #808080;
                border-bottom: 1px solid #808080;
            }

            .table.table-bordered-no-column tbody tr:first-child {
                border-top: none !important;
                border-bottom:  none !important;
            }

            .table-default thead {
                border-bottom: 2pt solid #0072ff !important;
                background-color: #ffffff;
            }

            .table-default tfoot td {
                border-top: 2pt solid #0072ff !important;
                background-color: #ffffff;
            }

            .table-default th {
                text-align: left;
            }

            .table-striped tbody>tr:nth-child(even) {
                background-color: #eaeaea
            }

            .table-default td, .table-default th {
                border: none !important;
                padding: 3pt 2pt;
                vertical-align: middle;
            }

            .title {
                font-size: 16pt;
                color: #0072ff
            }

            .text-center {
                text-align: center;
            }

            .text-left {
                text-align: left !important;
            }

            .text-right {
                text-align: right !important;
            }

            .pt-30 {
                padding-top: 30pt !important;
            }

            .pt-10 {
                padding-top: 10pt !important;
            }

            .pb-10 {
                padding-bottom: 10pt !important;
            }

            .mt-10 {
                margin-top: 10pt;
            }

            .mb-10 {
                margin-bottom: 10pt;
            }

            .mb-20 {
                margin-bottom: 20pt;
            }

            .mb-30 {
                margin-bottom: 30pt;
            }

            .logo {
                background-image: url({!! \iEducar\Packages\Reports\Services\Tenant::logoBase64() !!});
                width: 100px;
                height: 100px;
                background-repeat: no-repeat;
                background-size: contain;
            }

            .status-red {
                width: 24px;
                height: 24px;
                display: inline-block;
                background-image: url("data:image/svg+xml,%3Csvg width='27' height='24' viewBox='0 0 27 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg filter='url(%23filter0_d)'%3E%3Cpath d='M11.5 4.5L13.5 2.5H14L22 15.5L21 17.5L5.5 17L4.5 15.5L11.5 4.5Z' fill='%23FFE4EA'/%3E%3Cpath d='M20.9643 18H6.03571C4.91336 18 4 17.1027 4 16C4 15.608 4.10993 15.2413 4.31893 14.9347L11.7669 2.96C12.1184 2.372 12.7848 2 13.5 2C14.2152 2 14.8816 2.372 15.2385 2.97067L22.696 14.96C22.8901 15.2413 23 15.608 23 16C23 17.1027 22.0866 18 20.9643 18ZM13.5 3.45455C13.2639 3.45455 13.0467 3.57455 12.9327 3.76521L5.76292 15.6293C5.68828 15.7387 5.65842 15.8493 5.65842 15.9747C5.65842 16.3413 5.96378 16.6413 6.33699 16.6413H20.6335C21.0067 16.6413 21.312 16.3413 21.312 15.9747C21.312 15.8493 21.2822 15.7387 21.2238 15.6547L14.0741 3.77588C13.9533 3.57455 13.7361 3.45455 13.5 3.45455Z' fill='%23EC6F8C'/%3E%3Cline x1='13.75' y1='7.75' x2='13.75' y2='12.25' stroke='%23EC6F8C' stroke-width='1.5' stroke-linecap='round'/%3E%3Ccircle cx='13.75' cy='14.75' r='0.75' fill='%23EC6F8C'/%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='0' y='0' width='27' height='24' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0'/%3E%3CfeOffset dy='2'/%3E%3CfeGaussianBlur stdDeviation='2'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.925 0 0 0 0 0.435521 0 0 0 0 0.547733 0 0 0 0.18 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3C/defs%3E%3C/svg%3E%0A");
            }

            .status-green {
                width: 24px;
                height: 24px;
                display: inline-block;
                background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg filter='url(%23filter0_d)'%3E%3Crect x='4' y='2' width='16' height='16' rx='8' fill='%23CCFAB6'/%3E%3C/g%3E%3Cpath d='M15.7082 7.73781C15.3192 7.34912 14.6889 7.34912 14.3001 7.73781L11.0577 10.9801L9.9497 9.87211C9.56071 9.48342 8.93048 9.48342 8.54159 9.87211C8.1528 10.261 8.1528 10.8914 8.54159 11.2802L10.3536 13.0922C10.5481 13.2866 10.8027 13.3839 11.0576 13.3839C11.3125 13.3839 11.5672 13.2866 11.7617 13.0923L15.7082 9.14591C16.0969 8.75702 16.0969 8.1266 15.7082 7.73781Z' fill='%23298000'/%3E%3Cpath d='M12.1641 2C7.66238 2 4 5.66238 4 10.1641C4 14.6657 7.66238 18.3281 12.1641 18.3281C16.6657 18.3281 20.3281 14.6657 20.3281 10.1641C20.3281 5.66238 16.6657 2 12.1641 2ZM12.1641 16.3369C8.76035 16.3369 5.99123 13.5678 5.99123 10.1641C5.99123 6.76035 8.76035 3.99123 12.1641 3.99123C15.5678 3.99123 18.3369 6.76035 18.3369 10.1641C18.3369 13.5678 15.5678 16.3369 12.1641 16.3369Z' fill='%23298000'/%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='0' y='0' width='24' height='24' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0'/%3E%3CfeOffset dy='2'/%3E%3CfeGaussianBlur stdDeviation='2'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.160784 0 0 0 0 0.501961 0 0 0 0 0 0 0 0 0.12 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3C/defs%3E%3C/svg%3E%0A");
            }

            .status-yellow {
                width: 24px;
                height: 24px;
                display: inline-block;
                background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg filter='url(%23filter0_d)'%3E%3Crect x='4' y='2' width='16' height='16' rx='8' fill='%23FFF495'/%3E%3C/g%3E%3Cpath d='M12.332 1C7.18635 1 3 5.18635 3 10.332C3 15.4777 7.18635 19.6641 12.332 19.6641C17.4777 19.6641 21.6641 15.4777 21.6641 10.332C21.6641 5.18635 17.4777 1 12.332 1ZM12.332 16.9978C8.65654 16.9978 5.66629 14.0075 5.66629 10.332C5.66629 6.65654 8.65654 3.66629 12.332 3.66629C16.0075 3.66629 18.9978 6.65654 18.9978 10.332C18.9978 14.0075 16.0075 16.9978 12.332 16.9978Z' fill='%23998A00'/%3E%3Cpath d='M14.3331 9.5H13V8.16685C13 7.4306 12.7363 6 12 6C11.2637 6 11 6.92948 11 7.66573V10.332C11 11.0683 11.5969 11.6652 12.3331 11.6652H14.9994C15.7357 11.6652 16.5 11.2363 16.5 10.5C16.5 9.76375 15.0694 9.5 14.3331 9.5Z' fill='%23998A00'/%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='0' y='0' width='24' height='24' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0'/%3E%3CfeOffset dy='2'/%3E%3CfeGaussianBlur stdDeviation='2'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.12 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3C/defs%3E%3C/svg%3E%0A");
            }

            .status-purple {
                width: 24px;
                height: 24px;
                display: inline-block;
                background-image: url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 28 28' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cg filter='url(%23filter0_d)'%3E%3Crect x='4' y='2' width='19.5981' height='19.5981' rx='9.79904' fill='%23D0D2FE'/%3E%3C/g%3E%3Cpath d='M14 2C8.48598 2 4 6.48598 4 12C4 17.514 8.48598 22 14 22C19.514 22 24 17.514 24 12C24 6.48598 19.514 2 14 2ZM14 19.561C9.83085 19.561 6.43902 16.1691 6.43902 12C6.43902 7.83085 9.83085 4.43902 14 4.43902C18.1691 4.43902 21.561 7.83085 21.561 12C21.561 16.1691 18.1691 19.561 14 19.561Z' fill='%231900B4'/%3E%3Cg clip-path='url(%23clip0)'%3E%3Cpath d='M19 12.1016C19 11.6977 18.7752 11.3489 18.4444 11.1596V8.54705C18.4444 8.39751 18.3233 8 17.8889 8C17.7653 8 17.6425 8.04067 17.542 8.11997L16.0658 9.28259C15.3243 9.86604 14.3934 10.1875 13.4444 10.1875H10.1111C9.4974 10.1875 9 10.6771 9 11.2812V12.9219C9 13.526 9.4974 14.0156 10.1111 14.0156H10.6962C10.672 14.1947 10.6583 14.3769 10.6583 14.5625C10.6583 15.2422 10.8191 15.8844 11.1021 16.4584C11.1922 16.6411 11.3889 16.75 11.5951 16.75H12.8847C13.337 16.75 13.6085 16.24 13.3344 15.8859C13.0497 15.5182 12.8804 15.0595 12.8804 14.5625C12.8804 14.3726 12.9085 14.1901 12.9569 14.0156H13.4444C14.3934 14.0156 15.3243 14.3371 16.0656 14.9205L17.5418 16.0832C17.6403 16.1607 17.7626 16.203 17.8887 16.2031C18.3214 16.2031 18.4443 15.8138 18.4443 15.6562V13.0437C18.7752 12.8542 19 12.5054 19 12.1016ZM17.3333 14.5184L16.7595 14.0666C15.822 13.3283 14.6444 12.9219 13.4444 12.9219V11.2812C14.6444 11.2812 15.822 10.8749 16.7595 10.1366L17.3333 9.68472V14.5184Z' fill='%231900B4'/%3E%3C/g%3E%3Cg filter='url(%23filter1_d)'%3E%3Crect x='4' y='2' width='19.5981' height='19.5981' rx='9.79904' fill='%23D0D2FE'/%3E%3C/g%3E%3Cpath d='M14 2C8.48598 2 4 6.48598 4 12C4 17.514 8.48598 22 14 22C19.514 22 24 17.514 24 12C24 6.48598 19.514 2 14 2ZM14 19.561C9.83085 19.561 6.43902 16.1691 6.43902 12C6.43902 7.83085 9.83085 4.43902 14 4.43902C18.1691 4.43902 21.561 7.83085 21.561 12C21.561 16.1691 18.1691 19.561 14 19.561Z' fill='%231900B4'/%3E%3Cg clip-path='url(%23clip1)'%3E%3Cpath d='M19 12.1016C19 11.6977 18.7752 11.3489 18.4444 11.1596V8.54705C18.4444 8.39751 18.3233 8 17.8889 8C17.7653 8 17.6425 8.04067 17.542 8.11997L16.0658 9.28259C15.3243 9.86604 14.3934 10.1875 13.4444 10.1875H10.1111C9.4974 10.1875 9 10.6771 9 11.2812V12.9219C9 13.526 9.4974 14.0156 10.1111 14.0156H10.6962C10.672 14.1947 10.6583 14.3769 10.6583 14.5625C10.6583 15.2422 10.8191 15.8844 11.1021 16.4584C11.1922 16.6411 11.3889 16.75 11.5951 16.75H12.8847C13.337 16.75 13.6085 16.24 13.3344 15.8859C13.0497 15.5182 12.8804 15.0595 12.8804 14.5625C12.8804 14.3726 12.9085 14.1901 12.9569 14.0156H13.4444C14.3934 14.0156 15.3243 14.3371 16.0656 14.9205L17.5418 16.0832C17.6403 16.1607 17.7626 16.203 17.8887 16.2031C18.3214 16.2031 18.4443 15.8138 18.4443 15.6562V13.0437C18.7752 12.8542 19 12.5054 19 12.1016ZM17.3333 14.5184L16.7595 14.0666C15.822 13.3283 14.6444 12.9219 13.4444 12.9219V11.2812C14.6444 11.2812 15.822 10.8749 16.7595 10.1366L17.3333 9.68472V14.5184Z' fill='%231900B4'/%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_d' x='0' y='0' width='27.5981' height='27.5981' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0' result='hardAlpha'/%3E%3CfeOffset dy='2'/%3E%3CfeGaussianBlur stdDeviation='2'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.160784 0 0 0 0 0.501961 0 0 0 0 0 0 0 0 0.12 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3Cfilter id='filter1_d' x='0' y='0' width='27.5981' height='27.5981' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB'%3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'/%3E%3CfeColorMatrix in='SourceAlpha' type='matrix' values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0' result='hardAlpha'/%3E%3CfeOffset dy='2'/%3E%3CfeGaussianBlur stdDeviation='2'/%3E%3CfeColorMatrix type='matrix' values='0 0 0 0 0.160784 0 0 0 0 0.501961 0 0 0 0 0 0 0 0 0.12 0'/%3E%3CfeBlend mode='normal' in2='BackgroundImageFix' result='effect1_dropShadow'/%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='effect1_dropShadow' result='shape'/%3E%3C/filter%3E%3CclipPath id='clip0'%3E%3Crect width='10' height='8.75' fill='white' transform='translate(9 8)'/%3E%3C/clipPath%3E%3CclipPath id='clip1'%3E%3Crect width='10' height='8.75' fill='white' transform='translate(9 8)'/%3E%3C/clipPath%3E%3C/defs%3E%3C/svg%3E");
            }

            body * {
                font-size: 8pt !important;
            }

            h1 {
                font-size: 14pt !important;
            }
        </style>

        @stack('css')
    </head>
    <body>
        <table class="table">
            <tr>
                <td class="text-center p-8 w-1" style="border-right: 5pt solid #0072ff">
                    <div class="logo"></div>
                </td>
                <td class="uppercase pb-10 mb-10 pl-15 pt-30" style="padding-left: 15pt">
                    <h3 style="color: #0072ff">
                        {{ mb_strtoupper($institution['nm_instituicao']) }}
                    </h3>
                    <br>
                    <h3 style="font-weight: normal !important;">
                        {{ $institution['logradouro'] }}, {{ $institution['numero'] }}<br>
                        {{ $institution['bairro'] }}<br>
                        {{ $institution['cidade'] }} / {{ $institution['ref_sigla_uf'] }}<br>
                        @if($institution['cep'])
                             {{ strlen($institution['cep']) === 8 ? substr($institution['cep'], 0, 5) . '-' . substr($institution['cep'], 5, 3) : $institution['cep'] }}<br>
                        @endif
                    </h3>
                </td>
                <td style="min-width: 270pt">
                    <table class="table table-p-1">
                        <tr>
                            <td class="text-right">
                                <b>Fone:</b> ({{ $institution['ddd_telefone'] }}) {{ $institution['telefone'] }} <br>
                                <b>Ano Letivo:</b> {{ $year }}<br>
                                <b>Emitido em:</b> {{ now()->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            @if($title)
                <tr>
                    <td colspan="3">
                        <h1 class="text-center mt-10 title">
                            {{ $title }}
                        </h1>
                    </td>
                </tr>
            @endif
        </table>
        <main class="pt-10">
            @yield('content')
        </main>
        @hasSection('footer')
            <footer>
                @yield('footer')
            </footer>
        @endif
    </body>
</html>
