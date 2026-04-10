<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de contacto</title>
</head>
<body style="font-family: sans-serif; color: #1a1a1a; background: #f5f5f5; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; padding: 32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 8px; overflow: hidden; max-width: 100%;">
                    <tr>
                        <td style="background: #0e1412; padding: 24px 32px;">
                            <p style="color: #d5e9dc; font-size: 22px; font-weight: bold; margin: 0;">ZIN</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; margin: 0 0 16px;">Nueva consulta de contacto</h1>
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding: 8px 0; color: #666; width: 100px;">Nombre:</td>
                                    <td style="padding: 8px 0; font-weight: 600;">{{ $senderName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666;">Email:</td>
                                    <td style="padding: 8px 0;">
                                        <a href="mailto:{{ $senderEmail }}" style="color: #2a7c4a;">{{ $senderEmail }}</a>
                                    </td>
                                </tr>
                            </table>
                            <hr style="border: none; border-top: 1px solid #e5e5e5; margin: 24px 0;" />
                            <p style="color: #444; white-space: pre-wrap; line-height: 1.6;">{{ $body }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background: #f0f0f0; padding: 16px 32px; font-size: 12px; color: #999; text-align: center;">
                            Este mensaje fue enviado desde el formulario de contacto de {{ config('app.name') }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
