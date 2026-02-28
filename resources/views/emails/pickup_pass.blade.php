<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 20px; }
        .ticket-container { max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 5px 0 0; font-size: 13px; opacity: 0.9; font-weight: 500; }
        
        .content { padding: 40px 30px; text-align: center; }
        .greeting { color: #1e293b; font-size: 16px; margin-bottom: 25px; }
        
        /* 🌟 QR Code 區域 */
        .qr-section { background-color: #ffffff; border: 2px dashed #cbd5e1; padding: 15px; display: inline-block; border-radius: 16px; margin-bottom: 30px; }
        .qr-section img { display: block; }
        
        /* 📋 詳情表格 */
        .details-table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: left; }
        .details-table th { color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 5px; font-weight: 800; }
        .details-table td { color: #1e293b; font-size: 15px; font-weight: 700; padding-bottom: 20px; }
        
        .footer { background-color: #f8fafc; padding: 25px; text-align: center; border-top: 1px solid #f1f5f9; }
        .token-label { font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
        .token-value { font-family: monospace; font-size: 14px; color: #64748b; background: #ffffff; padding: 4px 10px; border-radius: 4px; border: 1px solid #e2e8f0; }
        .warning { color: #ef4444; font-size: 11px; font-weight: 700; margin-top: 15px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>CONFIRMED</h1>
            <p>Official Digital Pickup Pass</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello, <strong>{{ $match->lostItem->passenger_name }}</strong>
            </div>

            <div class="qr-section">
                <img src="cid:{{ $qrCid }}" width="220" height="220" alt="Pickup QR Code">
            </div>

            <table class="details-table">
                <tr>
                    <th>📦 Item to Claim</th>
                </tr>
                <tr>
                    <td style="color: #2563eb;">{{ $match->foundItem->item_name }}</td>
                </tr>
                <tr>
                    <th>📅 Appointment Time</th>
                </tr>
                <tr>
                    <td>{{ \Carbon\Carbon::parse($match->appointment_at)->format('d M Y, h:i A') }}</td>
                </tr>
                <tr>
                    <th>📍 Pickup Location</th>
                </tr>
                <tr>
                    <td>{{ $match->appointment_venue ?? 'Airport Lost & Found Office' }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="token-label">Verification Token</div>
            <span class="token-value">{{ $match->verification_token }}</span>
            
            <div class="warning">
                ⚠️ Original ID / Passport Required for Verification
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; color: #94a3b8; font-size: 11px;">
        This is an automated message from the Secure Lost & Found System.
    </div>
</body>
</html>