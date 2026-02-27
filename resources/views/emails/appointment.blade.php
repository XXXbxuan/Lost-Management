<!DOCTYPE html>
<html>
<head>
    <title>Pickup Appointment</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; color: #333;">

    <h2 style="color: #2563eb;">Action Required: Confirm Your Pickup</h2>
    <p>Your lost item has been matched! Please confirm the following appointment to receive your verification QR code.</p>
    
    <div style="background: #f3f4f6; padding: 15px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2563eb;">
        <p style="margin: 5px 0;">
            <strong>📅 Scheduled Time:</strong> 
            {{ is_string($match->appointment_at) ? $match->appointment_at : $match->appointment_at->format('d M Y, h:i A') }}
        </p>
        <p style="margin: 5px 0;"><strong>📍 Location:</strong> {{ $match->appointment_venue ?? 'Admin Office' }}</p>
    </div>

    <p style="color: #666; font-size: 14px;">
        Important: You must click the button below to confirm your attendance. Once confirmed, your unique QR code will be displayed.
    </p>

    <div style="text-align:center;margin-top:30px;">
        <a href="{{ $confirmLink }}"
            style="display:inline-block;background:#16a34a;color:#fff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:bold;box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            CONFIRM & GET MY QR CODE
        </a>
    </div>

    <p style="margin-top: 40px; font-size: 11px; color: #aaa; text-align: center;">
        Ref ID: #{{ $match->id }} | This appointment will expire 24 hours after the scheduled time.
    </p>
</body>
</html>