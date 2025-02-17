<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Updated</title>
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
            color: #FFA500;
            /* لون برتقالي للإشارة للتحديث */
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

        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .button {
            background-color: #FFA500;
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
            Task Updated
        </div>
        <div class="content">
            <p>Hello {{ $userName }},</p>
            <p>A task has been updated in the project <span class="project-name">{{ $projectName }}</span>.</p>
            <p>Title: {{ $title }}</p>
            <p>Description: {{ $description }}</p>
            <p>Due Date: {{ $due_date }}</p>
            <p>Please review the updated details and make sure to complete it by the due date.</p>

        </div>
        <div class="footer">
            <p>If you have any questions or issues, please feel free to contact us.</p>
            <p>Thank you,</p>
            <p>The AlHussien Team</p>
        </div>
    </div>
</body>

</html>
