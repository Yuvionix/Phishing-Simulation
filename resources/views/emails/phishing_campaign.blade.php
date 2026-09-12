<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f4f4; margin:0; padding:24px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="padding:24px 32px;">
                            <p style="font-size:15px; color:#1c1c1c; line-height:1.5;">
                                {{ $body }}
                            </p>

                            <p style="text-align:center; margin:32px 0;">
                                <a href="{{ $trackingUrl }}"
                                   style="background:#1877F2; color:#ffffff; text-decoration:none;
                                          padding:12px 28px; border-radius:6px; font-size:15px; display:inline-block;">
                                    View Details
                                </a>
                            </p>

                            <p style="font-size:12px; color:#888888;">
                                If the button above doesn't work, copy and paste this link into your browser:<br>
                                <span style="word-break:break-all;">{{ $trackingUrl }}</span>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
