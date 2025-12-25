<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Account Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }

        .credentials-box {
            background-color: white;
            border: 2px solid #4F46E5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
        }

        .credential-label {
            font-weight: bold;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }

        .credential-value {
            font-size: 16px;
            color: #111827;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }

        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Welcome to {{ $schoolName }}</h1>
    </div>

    <div class="content">
        <p>Dear {{ $guardianName }},</p>

        <p>A student account has been successfully created for <strong>{{ $studentName }}</strong> at
            {{ $schoolName }}.</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #4F46E5;">Guardian Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Guardian Name</div>
                <div class="credential-value">{{ $guardianName }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Email / Username</div>
                <div class="credential-value">{{ $guardianEmail }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password</div>
                <div class="credential-value"
                    style="font-family: monospace; background-color: #fef3c7; padding: 10px; border-radius: 4px;">
                    {{ $guardianPassword }}
                </div>
            </div>
        </div>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #4F46E5;">Student Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Student Name</div>
                <div class="credential-value">{{ $studentName }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Email / Username</div>
                <div class="credential-value">{{ $studentEmail }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password</div>
                <div class="credential-value"
                    style="font-family: monospace; background-color: #fef3c7; padding: 10px; border-radius: 4px;">
                    {{ $studentPassword }}
                </div>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong> Please keep these credentials safe and secure. We recommend changing the
            password after the first login.
        </div>

        <p>The student can now log in to the school portal using these credentials to access:</p>
        <ul>
            <li>Class schedules and assignments</li>
            <li>Grades and academic reports</li>
            <li>School announcements</li>
            <li>Learning resources</li>
        </ul>

        <p>If you have any questions or need assistance, please don't hesitate to contact the school administration.</p>

        <p>Best regards,<br>
            <strong>{{ $schoolName }}</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>

</html>
