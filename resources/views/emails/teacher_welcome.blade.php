<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $schoolName }}</title>
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
            background-color: #10b981;
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
            border: 2px solid #10b981;
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
        <p>Dear {{ $teacherName }},</p>
        
        <p>Welcome to {{ $schoolName }}! We are delighted to have you join our teaching staff.</p>
        
        <p>Your teacher account has been successfully created. Below are your login credentials to access the school portal:</p>
        
        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #10b981;">Your Login Credentials</h3>
            
            <div class="credential-item">
                <div class="credential-label">Email / Username</div>
                <div class="credential-value">{{ $email }}</div>
            </div>
            
            <div class="credential-item">
                <div class="credential-label">Password</div>
                <div class="credential-value" style="font-family: monospace; background-color: #fef3c7; padding: 10px; border-radius: 4px;">
                    {{ $password }}
                </div>
            </div>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong> Please change your password immediately after your first login for security purposes.
        </div>
        
        <p>With your account, you can:</p>
        <ul>
            <li>Manage your classes and students</li>
            <li>Create and grade assignments</li>
            <li>Track student attendance</li>
            <li>Communicate with students and guardians</li>
            <li>Access teaching resources and materials</li>
        </ul>
        
        <p>If you have any questions or need assistance getting started, please don't hesitate to contact the school administration.</p>
        
        <p>We look forward to working with you!</p>
        
        <p>Best regards,<br>
        <strong>{{ $schoolName }} Administration</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
