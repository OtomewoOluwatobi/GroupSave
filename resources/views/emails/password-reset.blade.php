<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            color: #333;
            line-height: 1.6;
        }
        .content h2 {
            color: #667eea;
            font-size: 20px;
            margin-top: 0;
        }
        .reset-code {
            background-color: #f5f5f5;
            border: 2px dashed #667eea;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            margin: 25px 0;
        }
        .reset-code-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .reset-code-value {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            color: #667eea;
            font-weight: 700;
            letter-spacing: 2px;
            margin: 10px 0 0 0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #5568d3;
        }
        .warning {
            background-color: #ffe0e0;
            border: 1px solid #ff6b6b;
            color: #c92a2a;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .steps {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .steps ol {
            margin: 0;
            padding-left: 20px;
        }
        .steps li {
            margin: 10px 0;
            color: #333;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>GroupSave</h1>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Password Reset Request</p>
        </div>

        <div class="content">
            <h2>Reset Your Password</h2>

            <p>Hello {{ $name }},</p>

            <p>We received a request to reset the password for your GroupSave account. Use the reset code below to create a new password:</p>

            <div class="reset-code">
                <div class="reset-code-label">Your Reset Code</div>
                <div class="reset-code-value">{{ $resetCode }}</div>
            </div>

            <div class="steps">
                <strong>How to reset your password:</strong>
                <ol>
                    <li>Go to the GroupSave password reset page</li>
                    <li>Enter your email address: <strong>{{ $email }}</strong></li>
                    <li>Paste the reset code above</li>
                    <li>Create a new, strong password</li>
                    <li>Confirm and save your new password</li>
                </ol>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/reset-password?email={{ urlencode($email) }}" class="button">Reset Password</a>
            </div>

            <div class="warning">
                <strong>🔒 Security Alert:</strong> If you did not request this password reset, please ignore this email. Your account remains secure. If you believe someone is trying to access your account, please contact our support team immediately.
            </div>

            <p style="color: #666; font-size: 14px;">This password reset code will expire in <strong>24 hours</strong>. If it expires, you'll need to request a new one.</p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>The GroupSave Team</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin: 0;">For security reasons, never share this reset code with anyone.</p>
        </div>
    </div>
</body>
</html>
