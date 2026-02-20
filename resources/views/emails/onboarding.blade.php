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
        .features {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 4px;
            margin: 25px 0;
        }
        .feature-item {
            display: flex;
            margin: 15px 0;
            align-items: flex-start;
        }
        .feature-icon {
            font-size: 20px;
            margin-right: 15px;
            color: #667eea;
        }
        .feature-text {
            flex: 1;
        }
        .feature-text strong {
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        .feature-text p {
            margin: 0;
            color: #666;
            font-size: 14px;
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
        .verify-section {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 20px;
            border-radius: 4px;
            margin: 25px 0;
            text-align: center;
        }
        .verify-link {
            word-break: break-all;
            background-color: #ffffff;
            padding: 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 15px 0;
            color: #667eea;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .cta-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .cta-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .cta-list li:last-child {
            border-bottom: none;
        }
        .cta-list a {
            color: #667eea;
            text-decoration: none;
        }
        .cta-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>GroupSave</h1>
            <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Welcome to Smart Saving</p>
        </div>

        <div class="content">
            <h2>Welcome {{ $name }}! 🎉</h2>

            <p>Thank you for joining <strong>GroupSave</strong>! We're excited to help you achieve your savings goals with a community of like-minded savers.</p>

            <div class="features">
                <strong style="color: #667eea; display: block; margin-bottom: 15px;">Here's what you can do with GroupSave:</strong>
                
                <div class="feature-item">
                    <div class="feature-icon">👥</div>
                    <div class="feature-text">
                        <strong>Create or Join Groups</strong>
                        <p>Start a savings group with friends and family, or join existing groups to save together.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">💰</div>
                    <div class="feature-text">
                        <strong>Set Savings Goals</strong>
                        <p>Define target amounts and monthly contributions that work for your budget.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-text">
                        <strong>Track Progress</strong>
                        <p>Monitor your savings, member contributions, and group performance in real-time.</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-text">
                        <strong>Secure & Reliable</strong>
                        <p>Your financial data is encrypted and protected with enterprise-grade security.</p>
                    </div>
                </div>
            </div>

            <div class="verify-section">
                <strong>✉️ Verify Your Email</strong>
                <p style="margin: 10px 0;">Before you can start saving, please verify your email address by clicking the link below:</p>
                <a href="{{ $verifyLink }}" style="display: inline-block; background-color: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: 600;">Verify Email Now</a>
                <p style="margin: 15px 0 0 0; font-size: 12px;">Or use this verification link:</p>
                <div class="verify-link">{{ $verifyLink }}</div>
            </div>

            <p><strong>Here's what to do next:</strong></p>
            <ul class="cta-list">
                <li>✅ <a href="{{ config('app.frontend_url') }}/verify-email">Verify your email address</a></li>
                <li>📝 <a href="{{ config('app.frontend_url') }}/profile">Complete your profile</a></li>
                <li>💳 <a href="{{ config('app.frontend_url') }}/bank-accounts">Connect your bank account</a></li>
                <li>👫 <a href="{{ config('app.frontend_url') }}/groups/create">Create your first group</a></li>
            </ul>

            <p>If you have any questions or need help getting started, our support team is here for you. Feel free to reach out at <a href="mailto:support@groupsave.com" style="color: #667eea;">support@groupsave.com</a>.</p>

            <p style="margin-top: 30px;">
                Happy saving!<br>
                <strong>The GroupSave Team</strong>
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin: 0;">Start your journey towards financial freedom today!</p>
        </div>
    </div>
</body>
</html>
