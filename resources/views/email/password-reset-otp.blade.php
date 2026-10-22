<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="margin:0;padding:0;background:#f3f6f4;font-family:'Segoe UI',Arial,sans-serif;color:#111;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f3f6f4;padding:28px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(19,98,7,0.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#136207 0%,#000000 100%);padding:28px 24px;text-align:center;">
                            <img src="{{ url('/assets/img/logo.png') }}" width="72" alt="Rosenthal Barbershop" style="display:block;margin:0 auto 12px;border-radius:8px;">
                            <div style="color:#ffffff;font-size:13px;letter-spacing:0.18em;text-transform:uppercase;font-weight:700;">
                                Rosenthal Barbershop
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px 12px;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0b1b0a;">Your Password Reset OTP</h1>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;">
                                Hello {{ $emailData['name'] ?? 'Admin' }},
                            </p>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#475569;">
                                We received a request to reset your Rosenthal Barbershop admin account password.
                            </p>
                            <p style="margin:0 0 10px;font-size:14px;color:#64748b;text-align:center;">
                                Your verification code is:
                            </p>
                            <div style="margin:0 auto 22px;max-width:280px;background:#E6F2E5;border:1px solid #c7e0c4;border-radius:14px;padding:18px 12px;text-align:center;">
                                <span style="font-size:32px;letter-spacing:0.28em;font-weight:800;color:#136207;display:inline-block;">
                                    {{ $emailData['otp'] ?? '' }}
                                </span>
                            </div>
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#475569;text-align:center;">
                                This OTP will expire in <strong style="color:#136207;">{{ $emailData['expire_minutes'] ?? 10 }} minutes</strong>.
                            </p>
                            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:14px 16px;margin-bottom:8px;">
                                <p style="margin:0 0 8px;font-size:13px;line-height:1.55;color:#9a3412;">
                                    If you did not request this password reset, please ignore this email.
                                </p>
                                <p style="margin:0;font-size:13px;line-height:1.55;color:#9a3412;">
                                    Never share this OTP with anyone.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 28px 28px;text-align:center;">
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#94a3b8;">
                                © {{ date('Y') }} Rosenthal Barbershop<br>
                                Admin Portal Security Notice
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
