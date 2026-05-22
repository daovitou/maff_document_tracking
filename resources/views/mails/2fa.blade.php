
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your 2FA Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #2d9cdb;
            font-size: 24px;
        }

        .code-box {
            background-color: #f0f4f8;
            color: #2d9cdb;
            font-size: 32px;
            letter-spacing: 4px;
            text-align: center;
            padding: 20px;
            margin: 30px 0;
            border-radius: 6px;
            font-weight: bold;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Two-Factor Authentication Code : {{$user->facode}}</h1>
        </div>
        <div class="content">
            <p>To complete your login, please use the following 2FA code:</p>
            <p>This code will expire in 10 minutes. If you did not request this code, please ignore this email or
                contact support.</p>
            <p>Thank you,<br>MAFF Web Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MAFF Web. All rights reserved.
        </div>
    </div>
</body>

</html>
