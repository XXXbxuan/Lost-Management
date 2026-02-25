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

    <div style="text-align: center; margin: 30px 0;">
        <p style="font-weight: bold; color: #555;">Your Verification QR Code:</p>
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="250" style="border: 1px solid #ddd; padding: 10px;">
    </div>

    <p>Please click the button below to confirm you are coming to collect it:</p>

    <a href="{{ $confirmLink }}" style="display: inline-block; background-color: #16a34a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px;">
        YES, I Confirm My Pickup
    </a>

    <p style="margin-top: 30px; font-size: 12px; color: #888;">
        Ref ID: #{{ $match->id }} | TARUMT Airport Lost & Found
    </p>
</body>
</html>