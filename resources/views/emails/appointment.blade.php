<!DOCTYPE html>
<html>
<head>
    <title>Pickup Appointment</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; color: #333; background-color: #f9fafb;">

    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <h2 style="color: #1e40af; margin-top: 0;">Action Required: Confirm Your Pickup</h2>

        <p>Your lost item has been matched! Please review the appointment details below to proceed.</p>

        <div style="background: #eff6ff; padding: 20px; margin: 25px 0; border-radius: 12px; border-left: 5px solid #3b82f6;">
            <p style="margin: 8px 0; font-size: 16px;">
                <strong>📅 Scheduled Time:</strong><br>
                <span style="color: #1e40af;">
                    {{ is_string($match->appointment_at) ? $match->appointment_at : $match->appointment_at->format('d M Y, h:i A') }}
                </span>
            </p>

            <p style="margin: 8px 0; font-size: 16px;">
                <strong>📍 Location:</strong><br>
                <span style="color: #1e40af;">{{ $match->appointment_venue ?? 'Admin Office' }}</span>
            </p>
        </div>

        <p style="color: #4b5563; font-size: 14px; margin-bottom: 30px;">
            <strong>Important:</strong> You must confirm your attendance to receive your unique QR code.
            If this time does not work for you, please click "Reject" to request a reschedule.
        </p>

        <div style="text-align: center; margin-top: 20px;">
            <a
                href="{{ $confirmLink }}"
                style="display: inline-block; background: #16a34a; color: #ffffff; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: bold; margin: 10px; min-width: 200px;"
            >
                ✅ CONFIRM TIME
            </a>

            <a
                href="{{ route('pickup.reject', ['token' => $match->verification_token]) }}"
                style="display: inline-block; background: #ef4444; color: #ffffff; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: bold; margin: 10px; min-width: 200px;"
            >
                ❌ REJECT / RESCHEDULE
            </a>
        </div>

        <p style="margin-top: 40px; font-size: 12px; color: #9ca3af; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 20px;">
            Ref ID: #{{ $match->id }} | Airport Lost &amp; Found Management System<br>
            This link is secure and unique to your report.
        </p>
    </div>
</body>
</html>