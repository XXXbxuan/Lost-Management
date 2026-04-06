<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="background-color: #0f172a; padding: 40px 20px; margin: 0; min-height: 100%;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="color: #ffffff; font-family: sans-serif; font-size: 20px; margin: 0;">
            Airport Lost &amp; Found
        </h1>
    </div>

    <x-pickup-ticket
        :match="$match"
        :qrSource="$message->embedData($qrRaw, 'qr-code.png', 'image/png')"
    />

    <div style="text-align: center; margin-top: 30px;">
        <p style="color: #64748b; font-family: sans-serif; font-size: 12px;">
            This is an automated security pass. Please do not reply to this email.
        </p>
    </div>
</body>
</html>