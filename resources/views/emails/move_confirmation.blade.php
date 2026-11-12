<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your move is scheduled</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding:20px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                <tr>
                    <td style="padding:20px 0; text-align:center;">
                        <img src="cid:logo-1" alt="Golden Street Moving" style="max-width:180px; height:auto;">
                    </td>
                </tr>
                <tr>
                    <td style="background:linear-gradient(90deg,#f4b200,#f9d25b); padding:20px 24px; color:#1b1b1b;">
                        <h1 style="margin:0; font-size:22px;">Your move is scheduled</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 24px; font-size:14px; color:#333; line-height:1.5;">
                        <p style="margin:0 0 12px;">Hi {{ $event->customer_name ?: 'there' }},</p>
                        <p style="margin:0 0 16px;">Your move with Golden Street Moving is confirmed. Here are the details:</p>
                        <table width="100%" cellpadding="6" cellspacing="0" border="0" style="border-collapse:collapse; font-size:14px;">
                            <tr>
                                <td style="width:130px; color:#777;">Date</td>
                                <td><strong>{{ $event->start_at->format('l, F j, Y') }}</strong></td>
                            </tr>
                            <tr>
                                <td style="color:#777;">Arrival time</td>
                                <td><strong>{{ $event->start_at->format('g:i A') }}</strong></td>
                            </tr>
                            @if($event->origin_address)
                            <tr>
                                <td style="color:#777;">From</td>
                                <td>{{ $event->origin_address }}</td>
                            </tr>
                            @endif
                            @if($event->destination_address)
                            <tr>
                                <td style="color:#777;">To</td>
                                <td>{{ $event->destination_address }}</td>
                            </tr>
                            @endif
                        </table>
                        <p style="margin:20px 0 0;">We'll send you a reminder the day before. If anything changes, just reply to this email or give us a call.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 24px; background:#fafafa; font-size:12px; color:#888; text-align:center;">
                        Golden Street Moving
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
