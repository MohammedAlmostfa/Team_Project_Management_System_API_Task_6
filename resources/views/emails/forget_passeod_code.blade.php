<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .header {
            font-size: 24px;
            font-weight: bold;
            color: #444;
            margin-bottom: 20px;
        }

        .content {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .code {
            font-size: 20px;
            font-weight: bold;
            color: #007BFF;
            margin: 10px 0;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Password Reset Request
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>You have requested to reset your password. Please use the following code to proceed:</p>
            <div class="code">
                {{ $code }}
            </div>
            <p>This code is valid for <strong>1 hour only</strong>. Please do not share it with anyone.</p>
        </div>
        <div class="footer">
            <p>If you did not request a password reset, please ignore this email or contact support.</p>
            <p>Thank you,</p>
            <p>AlHussien Team</p>
        </div>
    </div>
</body>

</html>
