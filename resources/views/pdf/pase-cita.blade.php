@php
    $logoFile = public_path('images/logo.png');
    $logoBase64 = file_exists($logoFile) ? base64_encode(file_get_contents($logoFile)) : null;
    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->generate($qrUrl);
    $qrBase64 = base64_encode($qrSvg);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pase Digital SETC — Turno {{ str_pad((string)$cita->ID_Citas, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 24px;
        }

        .ticket-wrapper {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        /* ─── Cabecera ────────────────────────────────────────── */
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
            font-size: 16px;
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
        .header-status-cell {
            vertical-align: middle;
            text-align: right;
            width: 140px;
        }
        .status-badge {
            background-color: #064e3b;
            border: 1px solid #10b981;
            color: #85f8c4;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }

        /* ─── Contenido ───────────────────────────────────────── */
        .content-box {
            padding: 24px 20px;
            background: #ffffff;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-column {
            width: 42%;
            vertical-align: top;
            padding-right: 18px;
        }

        .qr-card {
            background-color: #eff4ff;
            border: 1px solid #dce9ff;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .qr-img-wrapper {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px;
            display: inline-block;
            margin: 0 auto;
        }
        .qr-img {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto;
        }
        .qr-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        .turno-code {
            font-size: 15px;
            font-weight: bold;
            color: #001229;
            margin-top: 3px;
            letter-spacing: 1px;
            font-family: monospace;
        }

        .data-column {
            width: 58%;
            vertical-align: top;
            padding-left: 6px;
        }

        .data-group {
            margin-bottom: 12px;
        }
        .data-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .data-value {
            font-size: 13px;
            font-weight: bold;
            color: #0b1c30;
        }
        .data-value-blue {
            color: #0051d5;
        }
        .data-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 1px;
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .sub-table td {
            vertical-align: top;
            width: 50%;
        }

        /* ─── Instrucciones ───────────────────────────────────── */
        .instructions-box {
            margin-top: 18px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .instructions-title {
            font-size: 10px;
            font-weight: bold;
            color: #001229;
            margin-bottom: 6px;
        }
        .instructions-list {
            padding-left: 14px;
            margin: 0;
            color: #475569;
            font-size: 9.5px;
            line-height: 1.45;
        }
        .instructions-list li {
            margin-bottom: 3px;
        }

        /* ─── Footer ──────────────────────────────────────────── */
        .footer-note {
            margin-top: 16px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        {{-- Cabecera Institucional --}}
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-logo-cell">
                    @if($logoBase64)
                        <img src="data:image/png;base64,{{ $logoBase64 }}" class="header-logo-img" alt="Logo SETC" />
                    @endif
                </td>
                <td class="header-title-cell">
                    <h1 class="header-title">SETC &bull; Pase Digital Oficial</h1>
                    <div class="header-subtitle">Sistema de Expedientes de Trámites y Citas</div>
                </td>
                <td class="header-status-cell">
                    <div style="font-size: 8.5px; color: #85f8c4; text-transform: uppercase; margin-bottom: 3px; font-weight: bold;">Estado del Turno</div>
                    <div class="status-badge">
                        {{ $cita->Estado_Citas ?? 'Confirmada' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Cuerpo del Pase --}}
        <div class="content-box">
            <table class="main-table" cellpadding="0" cellspacing="0">
                <tr>
                    {{-- Columna Izquierda: QR --}}
                    <td class="qr-column">
                        <div class="qr-card">
                            <div class="qr-img-wrapper">
                                <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" class="qr-img" alt="Código QR del Turno" />
                            </div>
                            <div class="qr-label">Código de Validación</div>
                            <div class="turno-code">TURNO-{{ str_pad((string)$cita->ID_Citas, 5, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </td>

                    {{-- Columna Derecha: Datos del Solicitante y Turno --}}
                    <td class="data-column">
                        <div class="data-group">
                            <div class="data-label">Ciudadano Solicitante</div>
                            <div class="data-value">{{ $cita->usuario?->Nombre ?? 'Ciudadano Registrado' }}</div>
                            <div class="data-sub">{{ $cita->usuario?->Email ?? '' }}</div>
                        </div>

                        <table class="sub-table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <div class="data-label">Fecha Programada</div>
                                    <div class="data-value data-value-blue" style="font-size: 11.5px;">
                                        {{ $cita->Fecha ? \Carbon\Carbon::parse($cita->Fecha)->translatedFormat('l, d \d\e F \d\e Y') : '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-label">Horario de Turno</div>
                                    <div class="data-value">{{ substr((string)$cita->Hora, 0, 5) }} hrs</div>
                                </td>
                            </tr>
                        </table>

                        @if ($cita->solicitud)
                            <div class="data-group">
                                <div class="data-label">Expediente Vinculado</div>
                                <div class="data-value data-value-blue" style="font-family: monospace; font-size: 12px;">
                                    {{ $cita->solicitud->folio?->Codigo_Folio ?? '#' . $cita->solicitud->ID_Solicitud }}
                                </div>
                                <div class="data-sub">{{ $cita->solicitud->versionTramite?->tipoTramite?->Nombre }}</div>
                            </div>
                        @endif

                        @if ($cita->revisor)
                            <div class="data-group">
                                <div class="data-label">Especialista / Revisor Técnico Asignado</div>
                                <div class="data-value" style="font-size: 11px;">{{ $cita->revisor->Nombre }}</div>
                                <div class="data-sub">{{ $cita->revisor->Email }}</div>
                            </div>
                        @endif

                        <div class="data-group" style="margin-bottom: 0;">
                            <div class="data-label">Sede y Módulo de Atención</div>
                            <div class="data-value" style="font-size: 11px;">Sede Central &bull; Mesa de Dictamen Ciudadano</div>
                            <div class="data-sub">Ventanilla 08 &bull; Atención de Trámites y Citas</div>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Instrucciones --}}
            <div class="instructions-box">
                <div class="instructions-title">Recomendaciones para el día de tu turno:</div>
                <ul class="instructions-list">
                    <li>Presenta este comprobante (impreso o en la pantalla de tu móvil) 10 minutos antes de tu cita.</li>
                    <li>Lleva contigo una identificación oficial vigente con fotografía (INE / Pasaporte).</li>
                    <li>El código QR permite validar de inmediato la autenticidad y estado de tu turno en ventanilla.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Pie de página --}}
    <div class="footer-note">
        Documento oficial generado por <strong>SETC</strong> — Sistema de Expedientes de Trámites y Citas &nbsp;|&nbsp;
        {{ now()->translatedFormat('d \d\e F \d\e Y, H:i') }} hrs &nbsp;|&nbsp;
        Válido únicamente para la fecha y horario especificados.
    </div>

</body>
</html>
