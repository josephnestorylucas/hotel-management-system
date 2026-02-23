<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1e3a5f; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.8; }
        .body { padding: 32px; }
        .footer { background: #f5f5f5; padding: 16px 32px; font-size: 12px; color: #999; text-align: center; }
        .btn { display: inline-block; background: #1e3a5f; color: #fff; padding: 10px 20px;
               border-radius: 4px; text-decoration: none; font-size: 14px; margin-top: 16px; }
        table.details { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.details th { background: #f5f5f5; text-align: left; padding: 8px 12px; font-size: 13px; }
        table.details td { padding: 8px 12px; font-size: 13px; border-bottom: 1px solid #eee; }
        .highlight { background: #f0f7ff; border-left: 4px solid #1e3a5f; padding: 12px 16px; margin: 16px 0; }
        ul { padding-left: 20px; }
        ul li { margin-bottom: 6px; font-size: 14px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Grand Hotel</h1>
        <p>Your comfort is our priority</p>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        Grand Hotel &middot; P.O. Box 000, Dar es Salaam, Tanzania &middot; +255 xxx xxx xxx<br>
        This email was sent to {{ $email ?? 'you' }}. If you have any questions, contact our front desk.
    </div>
</div>
</body>
</html>
