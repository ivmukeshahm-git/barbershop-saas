<!DOCTYPE html>

<html>

<head>

  <meta charset="UTF-8">

  <title>Barber Slot Booking Confirmation</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body style="background:#f6f6f6;margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f6f6f6">

    <tr>

      <td align="center">

        <table width="540" style="max-width:96vw;background:#fff;border-radius:14px;box-shadow:0 4px 16px #22334414;padding:34px 22px 22px 22px;" cellpadding="0" cellspacing="0" border="0">

          <tr>

            <td align="center" style="padding-bottom:18px;">

              <img src="{{ url('/assets/img/logo.pn?id=1') }}" width="110" alt="Rosenthal Barbershop" style="border-radius:5px;display:block;">

            </td>

          </tr>

          <tr>

            <td align="center" style="color:#1a2233;font-size:1.23em;font-family:'Segoe UI Semibold',Arial,sans-serif;padding-bottom:12px;font-weight:bold;letter-spacing:0.01em;">

              Barber Slot Booking Confirmed!

            </td>

          </tr>

          <tr>

            <td style="padding:16px 0 6px 0;font-size:1em;border-bottom:1px solid #ececec;">

              <span style="color:#8a95a7;">Hello</span>

              <span style="color:#2539aa;font-weight:600;">Mukesh Suthar</span>

            </td>

          </tr>

          <tr>

            <td style="padding:16px 0 6px 0;font-size:1em;border-bottom:1px solid #ececec;">

              <span style="color:#8a95a7;">Service</span>

              <span style="color:#2539aa;font-weight:600;">Men's Scissors Cut</span>

            </td>

          </tr>

          <tr>
            <td style="padding:16px 0 6px 0;font-size:1em;border-bottom:1px solid #ececec;">
              <span style="color:#8a95a7;">Date</span>
              <span style="color:#2539aa;font-weight:600;">12-12-2025</span>
            </td>
          </tr>

          <tr>

            <td style="padding:16px 0 6px 0;font-size:1em;border-bottom:1px solid #ececec;">

              <span style="color:#8a95a7;">Time</span>

              <span style="color:#2539aa;font-weight:600;">09:00 AM – 10:30 PM</span>

            </td>

          </tr>

          <tr>

            <td style="padding:16px 0 4px 0;font-size:1em;">

              <span style="color:#8a95a7;">Barber</span>

              <span style="color:#2539aa;font-weight:600;">Harmish Patel</span>

              <span style="color:#27ae60;font-weight:700;padding-left:10px;">Confirmed</span>

            </td>

          </tr>



          <!-- ✅ Added Section: Preparation Tips -->

          <tr>

            <td style="padding:20px 0 10px 0;font-size:1em;line-height:1.6;color:#333;">

              <b style="color:#1a2233;">Before Your Visit:</b>

              <ul style="margin:10px 0 0 18px;padding:0;color:#555;line-height:1.6;">

                <li>Please arrive 5–10 minutes early to relax before your service.</li>

                <li>Bring a photo reference if you want a specific hairstyle.</li>

                <li>Sanitize your hands upon arrival for your safety.</li>

              </ul>

            </td>

          </tr>



          <!-- ✅ Added Section: Cancellation Policy -->

          <tr>

            <td style="padding:16px 0;font-size:0.95em;color:#666;line-height:1.6;border-top:1px solid #ececec;">

              <b style="color:#1a2233;">Cancellation Policy:</b>  

              If you are unable to attend, please cancel or reschedule at least <b>12 hours</b> before your appointment to avoid charges.

            </td>

          </tr>



          <!-- ✅ Added Section: Social Media Links -->

          <tr>

            <td align="center" style="padding-top:20px;">

              <a href="{manage_booking_link}" style="display:block;width:100%;max-width:340px;margin:0 auto;background:#bd2130;color:#fff;text-align:center;border-radius:7px;padding:13px 0 12px 0;font-size:1.13em;text-decoration:none;font-weight:700;letter-spacing:.02em;font-family:'Segoe UI Semibold',Arial,sans-serif;">Visit Website</a>

            </td>

          </tr>

          <tr>

            <td align="center" style="color:#7b8596;font-size:1em;padding-top:29px;line-height:1.7;">

              Thank you for choosing us!<br/>

              <b style="color:#212a3d;font-size:1.08em;font-weight:700;letter-spacing:0.01em;">Rosenthal Barbershop</b><br/>

              <a href="tel:{{ optional($siteSetting)->phoneTel() ?: '+15875552489' }}" style="color:#2560e0;text-decoration:none;font-weight:500;">Call: {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}</a>

            </td>

          </tr>

        </table>

      </td>

    </tr>

  </table>

</body>

</html>

