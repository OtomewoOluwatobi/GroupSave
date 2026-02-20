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
        .link-text {
            word-break: break-all;
            background-color: #f5f5f5;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #666;
            border-left: 4px solid #667eea;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
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
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Email Verification</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }},</h2>

            <p>Thank you for signing up for <strong>GroupSave</strong>! We're thrilled to have you join our community of smart savers.</p>

            <p>To get started and unlock all features, please verify your email address by clicking the button below:</p>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
            </div>

            <p>Or copy and paste this link into your browser:</p>
            <div class="link-text">
                {{ $verificationUrl }}
            </div>

            <div class="warning">
                <strong>⏰ Time Sensitive:</strong> This verification link will expire in <strong>{{ $expiresIn }} minutes</strong>. Please verify your email soon.
            </div>

            <p>If you did not create this account or have any questions, please don't hesitate to <a href="mailto:support@groupsave.com" style="color: #667eea;">contact our support team</a>.</p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>The GroupSave Team</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin: 0;">If you're having trouble clicking the button, copy and paste the link above into your browser.</p>
        </div>
    </div>
</body>
</html>
