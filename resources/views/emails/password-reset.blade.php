<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña — SETC</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 580px; margin: 40px auto; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #001229 0%, #0051d5 100%); padding: 36px 40px; text-align: center; }
        .header-icon { width: 56px; height: 56px; background: rgba(255,255,255,0.15); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 8px 0 0; color: #93c5fd; font-size: 13px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 15px; color: #0f172a; font-weight: 600; margin-bottom: 12px; }
        .text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 28px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #0051d5; color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 10px; font-size: 14px; font-weight: 700; letter-spacing: 0.2px; }
        .btn:hover { background: #003ea8; }
        .notice { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-top: 24px; }
        .notice p { margin: 0; font-size: 12px; color: #64748b; line-height: 1.6; }
        .notice strong { color: #334155; }
        .url-fallback { word-break: break-all; color: #0051d5; font-size: 11px; margin-top: 8px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { margin: 0; font-size: 11px; color: #94a3b8; line-height: 1.6; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        {{-- Header --}}
        <div class="header">
            <div class="header-icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#ffffff" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                </svg>
            </div>
            <h1>Restablecer Contraseña</h1>
            <p>Portal de Servicios SETC</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">Hola, {{ $usuario->Nombre ?? $usuario->name }} 👋</p>
            <p class="text">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta en el <strong>portal SETC</strong>
                asociada al correo <strong>{{ $usuario->Email ?? $usuario->email }}</strong>.
            </p>
            <p class="text">
                Haz clic en el botón a continuación para crear una nueva contraseña. Este enlace es
                <strong>válido por 60 minutos</strong> y solo puede usarse una vez.
            </p>

            <div class="btn-wrap">
                <a href="{{ $resetUrl }}" class="btn">
                    🔐 &nbsp; Restablecer mi Contraseña
                </a>
            </div>

            <div class="notice">
                <p>
                    <strong>¿No solicitaste este cambio?</strong><br>
                    Si no fuiste tú quien inició esta solicitud, ignora este mensaje. Tu contraseña permanecerá sin cambios y el enlace expirará automáticamente.
                </p>
                <p class="url-fallback">Si el botón no funciona, copia y pega esta URL en tu navegador:<br>{{ $resetUrl }}</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>
                <strong>SETC — Sistema de Expedientes y Trámites Ciudadanos</strong><br>
                Este es un mensaje automático. Por favor no respondas a este correo.<br>
                © {{ date('Y') }} SETC. Todos los derechos reservados.
            </p>
        </div>
    </div>
</div>
</body>
</html>

