<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Book Your Next Barber Service</title>
</head>
<body style="background: #f1f4f9; font-family: Arial,sans-serif; margin:0; padding:0;">
  <div style="background:#fff; max-width:480px; margin:32px auto; border-radius:9px; box-shadow:0 2px 18px #27498518; border:1px solid #e0e8f5; padding:30px 22px 18px 22px;">
    <div style="text-align:center; margin-bottom:24px; border-bottom:1.5px solid #dde6fa; padding-bottom:16px;">
      <img src="{{ url('/assets/img/logo.png') }}" alt="Rosenthal Barbershop" style="width:150px; height:150px; border-radius:50%; margin-bottom:7px;">
      <div style="color:#2b4570; font-size:23px; font-weight:800; letter-spacing:1px; margin-bottom:3px;">Book Your Next Service!</div>
      <div style="color:#4266a6; font-size:15px;">Rosenthal Barbershop | {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}</div>
      <div style="color:#136207; font-size:13px; margin-top:2px;">{{ optional($siteSetting)->websiteHost() ?: 'www.bluecrownbarbers.com' }}</div>
    </div>
    <div style="color:#234060; font-size:16px; line-height:1.7;">
      Hi <b>{{ $user }}</b>,<br><br>
      Thank you for choosing Rosenthal Barbershop for your recent <b>{{ $serviceName }}</b>.<br><br>
      Keep your style fresh! Book your next appointment for a hair cut or any other service at your convenience.<br><br>
      <div style="text-align:center; margin-bottom:20px;">
        <a href="{{ url('/book') }}" style="display:inline-block; background:#1976d2; color:#fff !important; text-decoration:none; border-radius:5px; padding:12px 32px; font-weight:700; font-size:16px; margin:18px 0 6px 0; transition:background 0.22s;">Book Your Next Service</a>
      </div>
      <br>
      If you have any questions, call us at <b>{{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}</b>.<br>
      We look forward to serving you again!<br><br>
      Kind regards,<br>
      <b>The Rosenthal Barbershop Team</b>
    </div>
    <div style="color:#43516b; font-size:13px; text-align:center; margin-top:18px; border-top:1px solid #e8edf7; padding-top:8px;">
     {{ $siteSetting->contact_address ?? '245 King Street, Downtown, Toronto, Ontario, Canada' }}
    </div>
  </div>
</body>
</html>
