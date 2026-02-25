<!DOCTYPE html>
<html>
<head>
    <title>Pickup Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; color: #333;">

    <h2 style="color: #2563eb;">Good news! Your item has been found.</h2>
    <p>We have matched your lost item report with a found item.</p>
    
    <div style="background: #f3f4f6; padding: 15px; margin: 20px 0; border-radius: 8px;">
        <p style="margin: 5px 0;">
            <strong>📅 Time:</strong> 
            {{ is_string($match->appointment_at) ? $match->appointment_at : $match->appointment_at->format('d M Y, h:i A') }}
        </p>
        <p style="margin: 5px 0;"><strong>📍 Location:</strong> {{ $match->appointment_venue ?? 'Admin Office' }}</p>
    </div>

    <div style="text-align:center; margin:16px 0;">
    <img
        src="cid:{{ $qrCid }}"
        alt="QR Code"
        style="width:220px;height:220px;display:block;margin:0 auto;border-radius:12px;"
    >
    </div>

    <div style="text-align:center;margin-top:16px;">
    <a href="{{ $confirmLink }}"
        style="display:inline-block;background:#16a34a;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;">
        YES, I Confirm My Pickup
    </a>
    </div>

    <p style="margin-top: 30px; font-size: 12px; color: #888;">
        Ref ID: #{{ $match->id }} | TARUMT Airport Lost & Found
    </p>
</body>
</html>