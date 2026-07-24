<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
</head>

<body style="margin:0;padding:0;background:#eef2f7;font-family:Segoe UI,Arial,sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:40px 15px;">
<tr>
<td align="center">

<table role="presentation" width="620" cellpadding="0" cellspacing="0"
style="background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 10px 35px rgba(0,0,0,.08);">

<!-- HEADER -->
<tr>
<td style="
background:linear-gradient(135deg,#2563eb,#1d4ed8);
padding:45px 35px;
text-align:center;
color:#ffffff;
">

<div style="
width:72px;
height:72px;
margin:auto;
background:rgba(255,255,255,.18);
border-radius:50%;
line-height:72px;
font-size:34px;">
🔐
</div>

<h1 style="margin:20px 0 5px;font-size:30px;font-weight:700;">
Intern Boscod
</h1>

<p style="margin:0;font-size:16px;opacity:.95;">
Password Reset Verification
</p>

</td>
</tr>

<!-- CONTENT -->
<tr>
<td style="padding:45px;">

<span style="
display:inline-block;
background:#e0ecff;
color:#2563eb;
padding:8px 18px;
border-radius:30px;
font-size:13px;
font-weight:600;">
SECURITY VERIFICATION
</span>

<h2 style="
margin:25px 0 10px;
font-size:28px;
color:#111827;">
Halo 👋
</h2>

<p style="
font-size:16px;
line-height:1.8;
color:#4b5563;
margin:0 0 18px;">
Kami menerima permintaan untuk mereset password akun <b>Intern Boscod</b>.
</p>

<p style="
font-size:16px;
line-height:1.8;
color:#4b5563;
margin:0;">
Silakan gunakan kode OTP berikut untuk melanjutkan proses reset password.
Jangan bagikan kode ini kepada siapa pun.
</p>

<!-- OTP BOX -->
<table width="100%" cellpadding="0" cellspacing="0" style="margin:40px 0;">
<tr>
<td align="center">

<div style="
display:inline-block;
background:#f8fbff;
border:2px solid #dbeafe;
border-radius:18px;
padding:24px 50px;">

<div style="
font-size:13px;
font-weight:600;
letter-spacing:2px;
color:#6b7280;
margin-bottom:15px;">
KODE OTP
</div>

<div style="
font-size:44px;
font-weight:800;
letter-spacing:12px;
color:#2563eb;">
{{ $otp }}
</div>

</div>

</td>
</tr>
</table>

<!-- INFO -->
<table width="100%" cellpadding="0" cellspacing="0">

<tr>
<td style="
background:#fff8eb;
border-left:5px solid #f59e0b;
padding:18px 20px;
border-radius:8px;">

<div style="font-size:15px;color:#92400e;">
⏰ <b>Kode OTP hanya berlaku selama 5 menit.</b>
<br><br>
Apabila masa berlaku habis, silakan lakukan permintaan OTP baru.
</div>

</td>
</tr>

<tr><td height="18"></td></tr>

<tr>
<td style="
background:#fff1f2;
border-left:5px solid #ef4444;
padding:18px 20px;
border-radius:8px;">

<div style="
font-size:15px;
color:#991b1b;
line-height:1.7;">

🛡️ <b>Keamanan Akun</b>

<ul style="
margin:12px 0 0 20px;
padding:0;">

<li>Jangan pernah membagikan OTP kepada siapa pun.</li>

<li>Tim Intern Boscod tidak akan pernah meminta OTP Anda.</li>

<li>Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini.</li>

</ul>

</div>

</td>
</tr>

</table>

<hr style="
margin:40px 0;
border:none;
border-top:1px solid #e5e7eb;">

<p style="
margin:0;
font-size:15px;
line-height:1.8;
color:#6b7280;">

Apabila Anda mengalami kendala dalam proses reset password,
silakan hubungi administrator sistem.

</p>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="
background:#f8fafc;
padding:30px;
text-align:center;">

<div style="
font-size:18px;
font-weight:700;
color:#2563eb;">
Intern Boscod
</div>

<div style="
margin-top:10px;
font-size:13px;
line-height:1.8;
color:#6b7280;">

Email ini dikirim secara otomatis oleh sistem.
Mohon untuk tidak membalas email ini.

<br><br>

© {{ date('Y') }} Intern Boscod.
All Rights Reserved.

</div>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>