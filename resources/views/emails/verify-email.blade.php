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

        .greeting-title {
            font-size: 21px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 8px;
        }
        .greeting-body {
            font-size: 15px;
            color: #666;
            margin: 0 0 28px;
        }

        /* ── Section heading ── */
        .section-heading {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9b8ec4;
            font-weight: 700;
            margin: 0 0 14px;
        }

        /* ── Verify CTA card ── */
        .verify-card {
            background: linear-gradient(135deg, #f8f7ff 0%, #ede9ff 100%);
            border: 1px solid #d4c8f8;
            border-radius: 10px;
            padding: 28px 24px;
            text-align: center;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .verify-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .verify-card-icon { font-size: 44px; margin-bottom: 12px; }
        .verify-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .verify-card-desc {
            font-size: 14px;
            color: #6b5fa0;
            margin-bottom: 20px;
        }
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

        /* ── Link box ── */
        .link-section {
            margin-top: 18px;
        }
        .link-label {
            font-size: 12px;
            color: #9b8ec4;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            text-align: left;
        }
        .link-box {
            word-break: break-all;
            background-color: #fff;
            padding: 12px 14px;
            border-radius: 6px;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: #667eea;
            border: 1px solid #d4c8f8;
            border-left: 4px solid #667eea;
            text-align: left;
        }

        /* ── Warning ── */
        .warning {
            background-color: #fff9f0;
            border: 1px solid #f5c87a;
            border-left: 4px solid #f5a623;
            color: #7a4f00;
            padding: 16px 18px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .warning-icon { font-size: 18px; flex-shrink: 0; }

        /* ── What happens next ── */
        .benefits-row {
            display: flex;
            gap: 10px;
            margin-bottom: 28px;
        }
        .benefit-pill {
            flex: 1;
            background: #f9f8ff;
            border: 1px solid #e0d9f7;
            border-radius: 8px;
            padding: 14px 12px;
            text-align: center;
        }
        .benefit-icon { font-size: 22px; display: block; margin-bottom: 6px; }
        .benefit-text { font-size: 11px; color: #6b5fa0; font-weight: 600; }

        /* ── Support ── */
        .support-note {
            font-size: 13px;
            color: #888;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .support-note a { color: #667eea; font-weight: 600; }

        /* ── Footer ── */
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
        }

        @media screen and (max-width: 520px) {
            .content { padding: 24px 18px; }
            .benefits-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="header-icon">✉️</span>
            <h1>GroupSave</h1>
            <p class="header-sub">Email Verification</p>
        </div>

        <div class="content">
            <h2 class="greeting-title">Hello, {{ $user->name }}!</h2>
            <p class="greeting-body">
                Thanks for joining <strong>GroupSave</strong>! You're one step away from unlocking your savings journey. Please verify your email to activate your account.
            </p>

            <p class="section-heading">Verify your email address</p>
            <div class="verify-card">
                <div class="verify-card-icon">📧</div>
                <div class="verify-card-title">Confirm it's you</div>
                <div class="verify-card-desc">Click the button below to verify your email address and get started.</div>
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address →</a>
                <div class="link-section">
                    <div class="link-label">Or copy this link into your browser:</div>
                    <div class="link-box">{{ $verificationUrl }}</div>
                </div>
            </div>

            <div class="warning">
                <span class="warning-icon">⏰</span>
                <div>
                    <strong>Time Sensitive:</strong> This verification link will expire in <strong>{{ $expiresIn }} minutes</strong>. Please verify your email soon.
                </div>
            </div>

            <p class="section-heading">After verification, you can</p>
            <div class="benefits-row">
                <div class="benefit-pill">
                    <span class="benefit-icon">🏦</span>
                    <div class="benefit-text">Create a Savings Group</div>
                </div>
                <div class="benefit-pill">
                    <span class="benefit-icon">👥</span>
                    <div class="benefit-text">Invite Members</div>
                </div>
                <div class="benefit-pill">
                    <span class="benefit-icon">📊</span>
                    <div class="benefit-text">Track Progress</div>
                </div>
                <div class="benefit-pill">
                    <span class="benefit-icon">💰</span>
                    <div class="benefit-text">Hit Your Goals</div>
                </div>
            </div>

            <p class="support-note">
                Didn't create an account? <a href="mailto:support@groupsave.com">Contact our support team</a>
            </p>
        </div>

        <div class="footer">
            <p style="margin:0 0 6px;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin:0;">If you're having trouble, copy and paste the verification link above.</p>
        </div>
    </div>
</body>
</html>
