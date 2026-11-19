@php
    $logoFile = public_path('images/logo.png');
    $logoBase64 = file_exists($logoFile) ? base64_encode(file_get_contents($logoFile)) : null;
    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(140)->generate($qrUrl);
    $qrBase64 = base64_encode($qrSvg);
    $esFavorable = $resolucion->Tipo_Resolucion === 'Favorable';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resolución Formal — {{ $folio }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 24px;
        }

        .doc-wrapper {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .header-table {
            width: 100%;
            background-color: #001229;
            color: #ffffff;
            padding: 16px 20px;
        }
        .header-logo-cell {
            vertical-align: middle;
            width: 54px;
        }
        .header-logo-img {
            height: 44px;
            width: auto;
            display: block;
        }
        .header-title-cell {
            vertical-align: middle;
            padding-left: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #ffffff;
            margin: 0;
            line-height: 1.2;
        }
        .header-subtitle {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .res-num-cell {
            vertical-align: middle;
            text-align: right;
            width: 140px;
        }
        .res-badge {
            background-color: #0051d5;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            font-family: monospace;
            display: inline-block;
        }

        .content-box {
            padding: 22px 20px;
            background: #ffffff;
        }

        .tipo-badge-wrap {
            margin-bottom: 20px;
        }
        .badge-favorable {
            background-color: #ecfdf5;
            border: 1.5px solid #10b981;
            color: #065f46;
            padding: 7px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-desfavorable {
            background-color: #fff1f2;
            border: 1.5px solid #f43f5e;
            color: #9f1239;
            padding: 7px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .col-left {
            width: 55%;
            vertical-align: top;
            padding-right: 15px;
        }
        .col-right {
            width: 45%;
            vertical-align: top;
            padding-left: 15px;
        }

        .dato-grupo {
            margin-bottom: 12px;
        }
        .dato-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .dato-valor {
            font-size: 12.5px;
            font-weight: bold;
            color: #0b1c30;
        }
        .dato-azul {
            color: #0051d5;
        }
        .dato-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 1px;
        }

        .qr-card {
            background-color: #eff4ff;
            border: 1px solid #dce9ff;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            margin-top: 6px;
        }
        .qr-img {
            width: 130px;
            height: 130px;
            display: block;
            margin: 0 auto;
            background: #fff;
            padding: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .qr-label {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 6px;
            font-family: monospace;
        }

        .sello-wrapper {
            text-align: center;
            margin: 18px 0;
        }
        .sello-box {
            border: 2.5px dashed #0051d5;
            border-radius: 50%;
            width: 95px;
            height: 95px;
            margin: 0 auto;
            text-align: center;
            padding-top: 18px;
            font-size: 8.5px;
            font-weight: bold;
            color: #0051d5;
            text-transform: uppercase;
            line-height: 1.25;
            opacity: 0.75;
        }

        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 9px;
            color: #475569;
            line-height: 1.45;
        }
        .info-title {
            font-weight: bold;
            color: #001229;
            margin-bottom: 4px;
            font-size: 9.5px;
        }

        .footer {
            margin-top: 14px;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="doc-wrapper">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-logo-cell">
                    @if($logoBase64)
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="header-logo-img" alt="Logo SETC" />
                    @endif
                </td>
                <td class="header-title-cell">
                    <h1 class="header-title">SETC &bull; Resolución Formal Institucional</h1>
                    <div class="header-subtitle">Sistema de Expedientes de Trámites y Citas &bull; Dictamen Oficial</div>
                </td>
                <td class="res-num-cell">
                    <div class="res-badge">
                        RES-{{ str_pad((string)$resolucion->ID_Resolucion, 5, '0', STR_PAD_LEFT) }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="content-box">
            <div class="tipo-badge-wrap">
                <div class="{{ $esFavorable ? 'badge-favorable' : 'badge-desfavorable' }}">
                    {{ $esFavorable ? '✓ Dictamen Favorable (Aprobado)' : '✗ Dictamen ' . $resolucion->Tipo_Resolucion }}
                </div>
            </div>

            <table class="main-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="col-left">
                        <div class="dato-grupo">
                            <div class="dato-label">Número de Folio / Expediente</div>
                            <div class="dato-valor dato-azul" style="font-family: monospace; font-size: 13px;">{{ $folio }}</div>
                        </div>

                        <div class="dato-grupo">
                            <div class="dato-label">Tipo de Trámite Institucional</div>
                            <div class="dato-valor">{{ $solicitud->versionTramite?->tipoTramite?->Nombre ?? 'N/D' }}</div>
                            <div class="dato-sub">Versión {{ $solicitud->versionTramite?->No_Version ?? '1.0' }}</div>
                        </div>

                        <div class="dato-grupo">
                            <div class="dato-label">Ciudadano Solicitante</div>
                            <div class="dato-valor">{{ $solicitud->solicitante?->Nombre ?? 'N/D' }}</div>
                            <div class="dato-sub">{{ $solicitud->solicitante?->Email ?? '' }}</div>
                        </div>

                        <div class="dato-grupo">
                            <div class="dato-label">Estado Definitivo del Expediente</div>
                            <div class="dato-valor">{{ $solicitud->Estado_Solicitud }}</div>
                        </div>
                    </td>

                    <td class="col-right">
                        <div class="dato-grupo">
                            <div class="dato-label">Fecha y Hora de Emisión</div>
                            <div class="dato-valor" style="font-size: 11px;">
                                {{ $resolucion->Fecha_Emision_Resolucion
                                    ? \Carbon\Carbon::parse($resolucion->Fecha_Emision_Resolucion)->translatedFormat('d \d\e F \d\e Y, H:i \h\r\s')
                                    : now()->translatedFormat('d \d\e F \d\e Y, H:i \h\r\s') }}
                            </div>
                        </div>

                        <div class="dato-grupo">
                            <div class="dato-label">Autoridad Emisora / Supervisor</div>
                            <div class="dato-valor" style="font-size: 11px;">{{ $resolucion->supervisor?->Nombre ?? 'Supervisor Institucional' }}</div>
                            <div class="dato-sub">{{ $resolucion->supervisor?->Email ?? '' }}</div>
                        </div>

                        <div class="qr-card">
                            <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" class="qr-img" alt="Validación QR" />
                            <div class="qr-label">Validación de Autenticidad</div>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="sello-wrapper">
                <div class="sello-box">
                    Resolución<br>Formal<br>Emitida<br>SETC {{ now()->format('Y') }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-title">Vigencia y Validez Jurídica</div>
                El presente documento certifica la resolución formal adoptada por la autoridad competente en la Mesa de Control y Dictamen del SETC. El código QR anexo permite a cualquier ciudadano, dependencia o auditor verificar la autenticidad e inmutabilidad de este expediente en el portal oficial.
            </div>
        </div>
    </div>

    <div class="footer">
        Documento oficial generado por <strong>SETC</strong> — Sistema de Expedientes de Trámites y Citas &nbsp;|&nbsp;
        Resolución N° RES-{{ str_pad((string)$resolucion->ID_Resolucion, 5, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp;
        Expediente {{ $folio }}
    </div>

</body>
</html>
