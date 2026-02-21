<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', 'Segoe UI', sans-serif;
            background-color: #f0f0f5;
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(102,126,234,0.12);
            overflow: hidden;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 44px 30px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -20px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .header-icon { font-size: 40px; display: block; margin-bottom: 10px; }
        .header h1 { margin: 0 0 6px; font-size: 28px; font-weight: 700; letter-spacing: -0.4px; }
        .header-sub { margin: 0; font-size: 13px; opacity: 0.85; }

        /* ── Content ── */
        .content {
            padding: 36px 32px;
            color: #333;
            line-height: 1.65;
        }

        .greeting { font-size: 15px; color: #555; margin-bottom: 16px; }
        .greeting strong { color: #333; font-size: 17px; }

        /* ── Section heading ── */
        .section-heading {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9b8ec4;
            font-weight: 700;
            margin: 0 0 14px;
        }

        /* ── Reset Code ── */
        .reset-code-box {
            background: #f9f8ff;
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 28px 20px;
            text-align: center;
            margin-bottom: 28px;
        }
        .reset-code-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9b8ec4;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .reset-code-value {
            font-family: 'DM Mono', monospace;
            font-size: 32px;
            color: #667eea;
            font-weight: 700;
            letter-spacing: 6px;
            display: block;
            margin-bottom: 10px;
        }
        .reset-code-expiry {
            font-size: 12px;
            color: #999;
        }

        /* ── Steps infographic ── */
        .steps-container {
            background: #f9f8ff;
            border: 1px solid #e0d9f7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .steps-title {
            font-size: 13px;
            font-weight: 700;
            color: #4a3f6b;
            margin-bottom: 16px;
        }
        .step-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }
        .step-row:last-child { margin-bottom: 0; }
        .step-num {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 11px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .step-connector {
            width: 2px;
            height: 14px;
            background: linear-gradient(to bottom, #764ba2, #e0d9f7);
            margin-left: 12px;
            margin-bottom: -4px;
        }
        .step-content { flex: 1; }
        .step-title { font-size: 13px; font-weight: 600; color: #333; }
        .step-detail { font-size: 12px; color: #888; margin-top: 1px; }
        .step-detail code {
            background: #ede9ff;
            padding: 1px 6px;
            border-radius: 3px;
            font-family: 'DM Mono', monospace;
            color: #4a3f6b;
            font-size: 11px;
        }

        /* ── CTA ── */
        .button-container { text-align: center; margin: 28px 0; }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 14px 44px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 14px rgba(102,126,234,0.35);
        }

        /* ── Warning ── */
        .warning {
            background-color: #ffe0e0;
            border: 1px solid #ff6b6b;
            border-left: 4px solid #e53e3e;
            color: #c92a2a;
            padding: 16px 18px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .warning-icon { font-size: 18px; flex-shrink: 0; }

        /* ── Footer ── */
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="header-icon">🔐</span>
            <h1>GroupSave</h1>
            <p class="header-sub">Password Reset Request</p>
        </div>

        <div class="content">
            <p class="greeting">Hello, <strong>{{ $name }}</strong></p>
            <p style="font-size:15px;color:#555;margin:0 0 28px;">We received a request to reset the password for your GroupSave account. Use the code below to create a new password.</p>

            <p class="section-heading">🔑 Your Reset Code</p>
            <div class="reset-code-box">
                <div class="reset-code-label">One-Time Reset Code</div>
                <span class="reset-code-value">{{ $resetCode }}</span>
                <div class="reset-code-expiry">⏰ Expires in <strong>24 hours</strong></div>
            </div>

            <p class="section-heading">How to reset your password</p>
            <div class="steps-container">
                <div class="steps-title">Follow these steps:</div>

                <div class="step-row">
                    <div class="step-num">1</div>
                    <div class="step-content">
                        <div class="step-title">Go to the Reset Page</div>
                        <div class="step-detail">Click the button below or visit the GroupSave reset page</div>
                    </div>
                </div>
                <div class="step-connector"></div>
                <div class="step-row">
                    <div class="step-num">2</div>
                    <div class="step-content">
                        <div class="step-title">Enter Your Email</div>
                        <div class="step-detail"><code>{{ $email }}</code></div>
                    </div>
                </div>
                <div class="step-connector"></div>
                <div class="step-row">
                    <div class="step-num">3</div>
                    <div class="step-content">
                        <div class="step-title">Paste the Reset Code</div>
                        <div class="step-detail">Use the code displayed above</div>
                    </div>
                </div>
                <div class="step-connector"></div>
                <div class="step-row">
                    <div class="step-num">4</div>
                    <div class="step-content">
                        <div class="step-title">Create a New Password</div>
                        <div class="step-detail">Choose a strong, unique password</div>
                    </div>
                </div>
                <div class="step-connector"></div>
                <div class="step-row">
                    <div class="step-num">5</div>
                    <div class="step-content">
                        <div class="step-title">Save & Log In</div>
                        <div class="step-detail">Confirm your new password and you're all set</div>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/reset-password?email={{ urlencode($email) }}" class="button">Reset My Password →</a>
            </div>

            <div class="warning">
                <span class="warning-icon">🚨</span>
                <div>
                    <strong>Security Alert:</strong> If you did not request this password reset, please ignore this email. Your account remains secure. If you believe someone is trying to access your account, contact our support team immediately.
                </div>
            </div>

            <p style="color:#666;font-size:13px;text-align:center;margin-top:20px;">
                For security reasons, <strong>never share this reset code</strong> with anyone.
            </p>
        </div>

        <div class="footer">
            <p style="margin:0 0 6px;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin:0;">For security reasons, never share this reset code with anyone.</p>
        </div>
    </div>
</body>
</html>
