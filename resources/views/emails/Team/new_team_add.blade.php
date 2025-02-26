<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to the New Team</title>
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

        .project-name {
            font-weight: bold;
            color: #007BFF;
        }

        .role {
            font-weight: bold;
            color: #28a745;
        }

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Welcome to the Team!
        </div>
        <div class="content">
            <p>Hello {{ $userName }},</p>
            <p>You have been added to the project <span class="project-name">{{ $projectName }}</span> as a <span
                    class="role">{{ $role }}</span>.</p>
            <p>We are excited to have you as part of the team!</p>
            <p>You can view and start collaborating on the project by clicking the link below:</p>
            <a href="{{ $projectUrl }}" class="button">View Project</a>
        </div>
        <div class="footer">
            <p>If you have any questions or issues, please feel free to contact us.</p>
            <p>Thank you,</p>
            <p> Al-Hussien-Team</p>
        </div>
    </div>
</body>

</html>
