<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="pt-br">
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="format-detection" content="date=no" />
  <meta name="format-detection" content="address=no" />
  <meta name="format-detection" content="telephone=no" />
  <meta name="x-apple-disable-message-reformatting" />
  <!--[if !mso]><!-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind|Muli&display=swap">
  <!--<![endif]-->
  <title>Protocolo</title>
  <style type="text/css" media="screen">
  body {
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
    min-width: 100% !important;
    width: 100% !important;
    background: #f6f9ff;
    -webkit-text-size-adjust: none;
    font-family: 'Hind', sans-serif;
  }

  a {
    color: #b04d4d;
    text-decoration: none
  }

  p {
    padding: 0 !important;
    margin: 0 !important
  }

  img {
    -ms-interpolation-mode: bicubic;
  }

  @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
    .m-shell {
      width: 100% !important;
      min-width: 100% !important;
    }

    .td {
      width: 100% !important;
      min-width: 100% !important;
    }
  }
  </style>
</head>
<body class="body" style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f6f9ff; -webkit-text-size-adjust:none;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f6f9ff" class="gwfw" role="presentation">
    <tr>
      <td align="center" valign="top" style="padding: 0 10px;">
        <table width="650" border="0" cellspacing="0" cellpadding="0" class="m-shell" role="presentation">
          <tr>
            <td class="td" bgcolor="#ffffff" style="border-radius: 6px; width:650px; min-width:650px; padding:0; margin:0; font-weight:normal;">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                <tr>
                  <td>
                    <div style="padding: 30px;">
                      <table style="width: 100%" role="presentation">
                        <tr>
                          <td style="font-size: 20px; color: gray; padding: 0 0 30px 0; vertical-align: middle">
                            Sua inscrição foi realizada com sucesso.
                          </td>
                          <td rowspan="7" valign="top">
                            <img style="float: right" src="{{ config('prematricula.logo') }}" width="100" alt="Logo">
                          </td>
                        </tr>
                      </table>
                      @foreach($preregistrations as $preregistration)
                      <table style="width: 100%; margin-top: 30px;" role="presentation">
                        <tr>
                          <td>
                            <strong style="font-size: 24px;">#{{ $preregistration->protocol }}</strong>
                            @if($preregistration->isRegistration())
                            <span style="margin-left: 10px; padding: 4px 8px 2px; border-radius: 8px; background: gray; font-size: 14px; text-transform: uppercase; background: #e7f2ff; border: 1px solid #0072ff; color: #0072ff; vertical-align: middle;">
                              Matrícula
                            </span>
                            @endif
                            @if($preregistration->isRegistrationRenewal())
                            <span style="margin-left: 10px; padding: 4px 8px 2px; border-radius: 8px; background: gray; font-size: 14px; text-transform: uppercase; background: #95D9FF; border: 1px solid #327FAA; color: #327FAA; vertical-align: middle;">
                             Rematrícula
                            </span>
                            @endif
                            @if($preregistration->isWaitingList())
                            <span style="margin-left: 10px; padding: 4px 8px 2px; border-radius: 8px; background: gray; font-size: 14px; text-transform: uppercase; background: #fff495; border: 1px solid #998a00; color: #998a00; vertical-align: middle;">
                              Lista de espera
                            </span>
                            @endif
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div style="text-transform: uppercase; font-size: 12px">
                              Data da solicitação
                            </div>
                            <div style="font-size: 16px; font-weight: bold">
                              {{ $preregistration->created_at->format('d/m/Y \à\s H:i:s') }}
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div style="text-transform: uppercase; font-size: 12px">
                              Nome do(a) aluno(a)
                            </div>
                            <div style="font-size: 16px; font-weight: bold">
                              {{ $preregistration->student->name }}
                            </div>
                          </td>
                        </tr>
                        @if($preregistration->process->show_priority_protocol === true)
                        <tr>
                            <td>
                                <div style="text-transform: uppercase; font-size: 12px">
                                    Posição
                                </div>
                                <div style="font-size: 16px; font-weight: bold">
                                    #{{ $preregistration->position }}
                                </div>
                                <div style="font-size: 10px">
                                    A posição pode sofrer mudanças de acordo com os atendimentos
                                    de prioridade do município.
                                </div>
                            </td>
                        </tr>
                        @endif
                        <tr>
                          <td>
                            <div style="text-transform: uppercase; font-size: 12px">
                              Série/Turno
                            </div>
                            <div style="font-size: 16px; font-weight: bold">
                              {{ $preregistration->grade->name }} / {{ $preregistration->period->name }}
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div style="text-transform: uppercase; font-size: 12px">
                              Escola
                            </div>
                            <div style="font-size: 16px; font-weight: bold">
                                <p>
                                    {{ $preregistration->school->name }}
                                </p>
                                @if($preregistration->school->phone)
                                    <p>
                            Telefone: {{ "({$preregistration->school->area_code}) {$preregistration->school->phone}" }}
                                    </p>
                                @endif
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td style="color: #6c757d;font-size: 80%;">
                            Código de autenticidade: {{ $preregistration->code }}
                          </td>
                        </tr>
                      </table>
                      @endforeach
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <table width="650" align="center" border="0" cellspacing="0" cellpadding="0" class="m-shell" role="presentation">
      <tbody>
        <tr>
            <td>
                <div style="padding: 30px;">
                    <p style="font-size: 15px; color: gray; text-align: center; vertical-align: middle;">
                        Este e-mail não está habilitado para respostas. Para mais informações favor contatar a Secretaria
                        escolar do Município ou a Unidade escolar selecionada na inscrição.
                    </p>
                </div>
            </td>
        </tr>
      </tbody>
  </table>
</body>
</html>
