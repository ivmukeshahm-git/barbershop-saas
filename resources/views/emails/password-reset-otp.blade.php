<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset code | {{ $emailData['shop_name'] ?? 'your shop' }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f6f4;font-family:'Segoe UI',Arial,sans-serif;color:#111;">
    @php
        $shopName = $emailData['shop_name'] ?? 'Your shop';
        $themeColor = $emailData['shop_theme_color'] ?? '#2D5BFF';
        $themeDark = $emailData['shop_theme_dark'] ?? '#18319A';
        $themeRgb = $emailData['shop_theme_rgb'] ?? '45, 91, 255';
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f6f4;padding:28px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba({{ $themeRgb }},0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeDark }} 100%);padding:28px 24px;text-align:center;">
                            @if (!empty($emailData['shop_logo_cid']))
                                <img src="cid:{{ $emailData['shop_logo_cid'] }}" width="72" alt="{{ $shopName }}" style="display:block;margin:0 auto 12px;border-radius:8px;">
                            @endif
                            <div style="color:#ffffff;font-size:13px;letter-spacing:0.12em;text-transform:uppercase;font-weight:700;">
                                {{ $shopName }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px 12px;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:{{ $themeDark }};">Password reset verification code</h1>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;">
                                Hello {{ $emailData['name'] ?? 'Admin' }},
                            </p>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#475569;">
                                We received a request to reset your admin password for {{ $shopName }}.
                            </p>
                            <p style="margin:0 0 10px;font-size:14px;color:#64748b;text-align:center;">
                                Your verification code is:
                            </p>
                            <div style="margin:0 auto 22px;max-width:280px;background:rgba({{ $themeRgb }},0.10);border:1px solid rgba({{ $themeRgb }},0.24);border-radius:14px;padding:18px 12px;text-align:center;">
                                <span style="font-size:32px;letter-spacing:0.28em;font-weight:800;color:{{ $themeDark }};display:inline-block;">
                                    {{ $emailData['otp'] ?? '' }}
                                </span>
                            </div>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;text-align:center;">
                                This verification code will expire in <strong style="color:{{ $themeDark }};">{{ $emailData['expire_minutes'] ?? 10 }} minutes</strong>.
                            </p>
                            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:8px;">
                                <p style="margin:0 0 8px;font-size:13px;line-height:1.55;color:#9a3412;">
                                    If you did not request this password reset, please ignore this email.
                                </p>
                                <p style="margin:0;font-size:13px;line-height:1.55;color:#9a3412;">
                                    Never share this verification code with anyone.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 28px 28px;text-align:center;">
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;">
                                © {{ date('Y') }} {{ $shopName }}<br>
                                Password reset security notice
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
