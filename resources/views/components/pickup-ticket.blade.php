@props(['match', 'qrSource'])

<div style="max-width: 420px; margin: 0 auto; background-color: #ffffff; border-radius: 35px; overflow: hidden; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-shadow: 0 20px 40px rgba(0,0,0,0.2); border: 1px solid #e2e8f0;">
    
    <div style="background-color: #10b981; background-image: linear-gradient(to right, #10b981, #059669); padding: 40px 20px; text-align: center; color: #ffffff;">
        <div style="display: inline-block; width: 50px; height: 50px; background-color: rgba(255,255,255,0.2); border-radius: 50%; line-height: 50px; margin-bottom: 15px;">
            <span style="font-size: 24px;">✓</span>
        </div>
        <h1 style="margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px;">Ready for Pickup</h1>
        <p style="margin: 5px 0 0; font-size: 13px; opacity: 0.9; font-weight: 500;">Valid at Lost & Found Counter</p>
    </div>

    <div style="background-color: #ffffff; padding: 30px; text-align: center;">
        
        <div style="display: inline-block; padding: 15px; background-color: #ffffff; border-radius: 20px; border: 2px dashed #e2e8f0; margin-bottom: 25px;">
            <img src="{{ $qrSource }}" alt="QR Code" width="220" height="220" style="display: block; border: 0;">
        </div>

        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border-radius: 20px; border-collapse: separate; text-align: left;">
            <tr>
                <td style="padding: 20px 20px 0 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
                                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Passenger</div>
                                <div style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ strtoupper($match->lostItem->passenger_name ?? 'N/A') }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 0;">
                                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Appointment</div>
                                <div style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ \Carbon\Carbon::parse($match->appointment_at)->format('d M, h:i A') }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 0;">
                                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Venue</div>
                                <div style="font-size: 15px; font-weight: 700; color: #1e293b;">{{ $match->appointment_venue ?? 'T1 Main Office' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0 20px 0;">
                                <div style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Item Ref</div>
                                <div style="font-size: 15px; font-weight: 700; color: #2563eb;">{{ $match->lostItem->item_name }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 20px; text-align: center;">
            <p style="margin: 0; font-size: 10px; color: #94a3b8; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;">TOKEN: {{ $match->verification_token }}</p>
        </div>

        <div style="margin-top: 20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <div style="background-color: #fff1f2; border: 1px solid #ffe4e6; border-radius: 50px; padding: 10px 20px; display: inline-block;">
                            <span style="font-size: 11px; color: #e11d48; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px;">🪪 Original ID Required</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="background-color: #1e293b; height: 15px; position: relative; border-top: 1px dashed #e2e8f0;">
        <table width="100%" height="15">
            <tr><td></td></tr>
        </table>
    </div>
</div>