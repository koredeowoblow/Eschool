<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ config('app.name') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello,</h2>
    <p>A new school account for <strong>{{ $schoolName }}</strong> has been created for you.</p>
    
    <p>You can login to the dashboard using the credentials below:</p>
    
    <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>Email:</strong> (The email address this was sent to)</p>
        <p><strong>Password:</strong> {{ $password }}</p>
    </div>

    <p>Please change your password immediately after logging in.</p>

    <p>
        <a href="{{ url('/login') }}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Login Now</a>
    </p>

    <p>Regards,<br>{{ config('app.name') }} Team</p>
</body>
</html>
