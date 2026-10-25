<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Código para redefinir sua senha</title>
</head>
<body style="margin:0; padding:24px; background:#f4f6f9; font-family:'Segoe UI', Roboto, Arial, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="background:#0d3b66; padding:20px 28px; color:#ffffff; font-size:18px; font-weight:700;">
                {{ config('amostraweb.name') }}
            </td>
        </tr>
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 12px;">Olá, {{ $usuario->name }}.</p>
                <p style="margin:0 0 20px;">Recebemos um pedido para redefinir a senha da sua conta ({{ $usuario->codigo_usuario }}). Use o código abaixo:</p>

                <p style="margin:0 0 20px; text-align:center; font-size:32px; font-weight:700; letter-spacing:8px; color:#0d3b66;">{{ $codigo }}</p>

                <p style="margin:0 0 12px;">O código vale por {{ $validadeMinutos }} minutos.</p>
                <p style="margin:0; color:#4b5563; font-size:14px;">Se você não pediu a redefinição, ignore este e-mail. Sua senha continua a mesma.</p>
            </td>
        </tr>
    </table>
</body>
</html>
