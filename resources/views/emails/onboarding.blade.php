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
        .logo-mark {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.4px;
        }
        .header-sub {
            margin: 0;
            font-size: 13px;
            opacity: 0.85;
        }

        /* ── Content ── */
        .content {
            padding: 36px 32px;
            color: #333;
            line-height: 1.65;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 8px;
        }
        .welcome-sub {
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

        /* ── Features infographic ── */
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 28px;
        }
        .feature-card {
            background: #f9f8ff;
            border: 1px solid #e0d9f7;
            border-radius: 10px;
            padding: 18px 16px;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .feature-icon {
            font-size: 26px;
            display: block;
            margin-bottom: 10px;
        }
        .feature-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .feature-desc {
            font-size: 12px;
            color: #777;
            line-height: 1.5;
            margin: 0;
        }

        /* ── Verify Section ── */
        .verify-section {
            background: #fff9f0;
            border: 1px solid #f5c87a;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .verify-header {
            background: #f5c87a;
            padding: 12px 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #7a4f00;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .verify-body {
            padding: 20px;
        }
        .verify-body p {
            color: #664400;
            font-size: 14px;
            margin: 0 0 16px;
        }
        .button-verify {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 3px 10px rgba(102,126,234,0.3);
        }
        .verify-link-box {
            margin-top: 14px;
            padding: 10px 14px;
            background: #fff;
            border: 1px solid #f0d9a0;
            border-radius: 6px;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: #667eea;
            word-break: break-all;
        }
        .verify-link-label {
            font-size: 11px;
            color: #b07820;
            font-weight: 600;
            margin-bottom: 4px;
        }

        /* ── Next Steps ── */
        .steps-list {
            list-style: none;
            padding: 0;
            margin: 0 0 28px;
        }
        .steps-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: #f9f8ff;
            border: 1px solid #e8e2f8;
            font-size: 14px;
            color: #333;
        }
        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 12px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step-icon { font-size: 18px; flex-shrink: 0; }
        .step-text { flex: 1; }
        .step-text a { color: #667eea; text-decoration: none; font-weight: 600; }

        /* ── Support note ── */
        .support-note {
            font-size: 14px;
            color: #666;
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
            .features-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo-mark">🏦</span>
            <h1>GroupSave</h1>
            <p class="header-sub">Welcome to Smart Saving</p>
        </div>

        <div class="content">
            <h2 class="welcome-title">Welcome, {{ $name }}! 🎉</h2>
            <p class="welcome-sub">We're excited to help you achieve your savings goals with a community of like-minded savers.</p>

            <p class="section-heading">What you can do with GroupSave</p>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon">👥</span>
                    <div class="feature-title">Create or Join Groups</div>
                    <p class="feature-desc">Start a savings group with friends and family, or join an existing one.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">💰</span>
                    <div class="feature-title">Set Savings Goals</div>
                    <p class="feature-desc">Define target amounts and monthly contributions that fit your budget.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📊</span>
                    <div class="feature-title">Track Progress</div>
                    <p class="feature-desc">Monitor contributions and group performance in real-time.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🔒</span>
                    <div class="feature-title">Secure & Reliable</div>
                    <p class="feature-desc">Your financial data is encrypted with enterprise-grade security.</p>
                </div>
            </div>

            <p class="section-heading">⚡ Action Required — Verify Your Email</p>
            <div class="verify-section">
                <div class="verify-header">✉️ Verify your email to unlock all features</div>
                <div class="verify-body">
                    <p>Please verify your email address before you can start saving:</p>
                    <a href="{{ $verifyLink }}" class="button-verify">Verify Email Now →</a>
                    <div class="verify-link-label" style="margin-top:14px;">Or paste this link into your browser:</div>
                    <div class="verify-link-box">{{ $verifyLink }}</div>
                </div>
            </div>

            <p class="section-heading">Your next steps</p>
            <ul class="steps-list">
                <li>
                    <div class="step-num">1</div>
                    <span class="step-icon">✅</span>
                    <span class="step-text"><a href="{{ config('app.frontend_url') }}/verify-email">Verify your email address</a></span>
                </li>
                <li>
                    <div class="step-num">2</div>
                    <span class="step-icon">📝</span>
                    <span class="step-text"><a href="{{ config('app.frontend_url') }}/profile">Complete your profile</a></span>
                </li>
                <li>
                    <div class="step-num">3</div>
                    <span class="step-icon">💳</span>
                    <span class="step-text"><a href="{{ config('app.frontend_url') }}/bank-accounts">Connect your bank account</a></span>
                </li>
                <li>
                    <div class="step-num">4</div>
                    <span class="step-icon">👫</span>
                    <span class="step-text"><a href="{{ config('app.frontend_url') }}/groups/create">Create your first group</a></span>
                </li>
            </ul>

            <p class="support-note">
                Questions? Our support team is here at <a href="mailto:support@groupsave.com">support@groupsave.com</a>
            </p>
        </div>

        <div class="footer">
            <p style="margin:0 0 6px;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin:0;">Start your journey towards financial freedom today!</p>
        </div>
    </div>
</body>
</html>
