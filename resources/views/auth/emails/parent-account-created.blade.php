<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your E-Fees Parent Account</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .title {
            font-size: 20px;
            color: #1f2937;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .welcome {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .account-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">E-Fees Portal</div>
            <h1 class="title">Parent Account Created</h1>
        </div>

        <div class="content">
            <p class="welcome">Dear {{ $parent->full_name }},</p>
            
            <p>Welcome to the E-Fees Portal! Your parent account has been successfully created by the school administration.</p>

            <div class="account-info">
                <strong>Your Account Information:</strong><br>
                Email: {{ $user->email }}<br>
                Account Type: Parent Account
            </div>

            <div class="security-note">
                <strong>🔒 Security Notice:</strong> For your security, you need to set your own password before accessing your account.
            </div>

            <p>To get started, please click the button below to set your password:</p>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Set Your Password</a>
            </div>

            <div class="info-box">
                <strong>Important:</strong><br>
                • This password reset link will expire in 60 minutes<br>
                • If you didn't request this account, please contact the school administration<br>
                • Keep your password secure and do not share it with others
            </div>

            <p>Once you set your password, you'll be able to:</p>
            <ul>
                <li>View your children's fee records and payment history</li>
                <li>Make online payments</li>
                <li>Receive payment reminders and notifications</li>
                <li>Update your contact information</li>
            </ul>
        </div>

        <div class="footer">
            <p>If you have any questions or need assistance, please contact the school administration.</p>
            <p>&copy; {{ date('Y') }} E-Fees Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
