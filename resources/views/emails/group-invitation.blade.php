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
        .header-icon {
            font-size: 42px;
            display: block;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .header-sub {
            margin: 0;
            font-size: 13px;
            opacity: 0.85;
            font-weight: 400;
        }

        /* ── Content ── */
        .content {
            padding: 36px 32px;
            color: #333;
            line-height: 1.65;
        }

        .greeting {
            font-size: 15px;
            color: #555;
            margin-bottom: 6px;
        }
        .greeting strong { color: #333; }

        .invite-headline {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 24px;
            line-height: 1.35;
        }
        .invite-headline span {
            color: #667eea;
        }

        /* ── Infographic: Inviter badge ── */
        .inviter-badge {
            display: flex;
            align-items: center;
            gap: 14px;
            background: linear-gradient(135deg, #f8f7ff 0%, #ede9ff 100%);
            border: 1px solid #d4c8f8;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }
        .inviter-avatar {
            width: 46px; height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .inviter-info {
            flex: 1;
        }
        .inviter-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #9b8ec4;
            font-weight: 600;
        }
        .inviter-name {
            font-size: 15px;
            font-weight: 700;
            color: #4a3f6b;
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

        /* ── Group Details Infographic ── */
        .group-details {
            background-color: #f9f8ff;
            border: 1px solid #e0d9f7;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .group-name-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 20px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .stat-cell {
            padding: 18px 20px;
            border-right: 1px solid #e0d9f7;
            border-bottom: 1px solid #e0d9f7;
            position: relative;
        }
        .stat-cell:nth-child(even) { border-right: none; }
        .stat-cell:nth-child(3),
        .stat-cell:nth-child(4) { border-bottom: none; }
        .stat-icon {
            font-size: 22px;
            display: block;
            margin-bottom: 6px;
        }
        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #9b8ec4;
            font-weight: 700;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            font-family: 'DM Mono', monospace;
            margin-top: 2px;
        }
        .stat-value.large-num { font-size: 22px; }

        /* ── Credentials table ── */
        .credentials-box {
            background: #fff9f0;
            border: 1px solid #f5c87a;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 28px;
        }
        .credentials-header {
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
        .credentials-row {
            display: flex;
            border-bottom: 1px solid #f5e2b0;
        }
        .credentials-row:last-child { border-bottom: none; }
        .cred-label {
            width: 120px;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #b07820;
            background: #fffaf0;
            border-right: 1px solid #f5e2b0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }
        .cred-value {
            padding: 14px 16px;
            font-family: 'DM Mono', monospace;
            font-size: 14px;
            color: #333;
            word-break: break-all;
            display: flex;
            align-items: center;
        }

        /* ── CTA Button ── */
        .button-container {
            text-align: center;
            margin: 28px 0;
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
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(102,126,234,0.35);
        }

        /* ── Disclaimer ── */
        .disclaimer {
            font-size: 13px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 4px;
        }

        /* ── Footer ── */
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
        }

        @media screen and (max-width: 600px) {
            .content { padding: 28px 20px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-value { font-size: 16px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="header-icon">🎉</span>
            <h1>You're Invited!</h1>
            <p class="header-sub">GroupSave — Smart Saving Together</p>
        </div>

        <div class="content">
            <p class="greeting">Hello, <strong>{{ $user->name }}</strong></p>
            <p class="invite-headline">
                You've been invited to join <span>"{{ $group->title }}"</span>
            </p>

            <p class="section-heading">Invitation from</p>
            <div class="inviter-badge">
                <div class="inviter-avatar">👤</div>
                <div class="inviter-info">
                    <div class="inviter-label">Invited by</div>
                    <div class="inviter-name">{{ $inviter->name }}</div>
                </div>
            </div>

            <p class="section-heading">Group Details</p>
            <div class="group-details">
                <div class="group-name-bar">
                    🏦 {{ $group->title }}
                </div>
                <div class="stats-grid">
                    <div class="stat-cell">
                        <span class="stat-icon">🎯</span>
                        <div class="stat-label">Target Amount</div>
                        <div class="stat-value large-num">£{{ number_format($group->target_amount, 2) }}</div>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-icon">📅</span>
                        <div class="stat-label">Monthly Contribution</div>
                        <div class="stat-value large-num">£{{ number_format($group->payable_amount, 2) }}</div>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-icon">🗓️</span>
                        <div class="stat-label">Start Date</div>
                        <div class="stat-value" style="font-size:14px;">{{ $group->expected_start_date }}</div>
                    </div>
                    <div class="stat-cell">
                        <span class="stat-icon">💳</span>
                        <div class="stat-label">Payment Day</div>
                        <div class="stat-value">{{ $group->payment_out_day }}<sup style="font-size:12px;color:#9b8ec4;">th</sup></div>
                    </div>
                </div>
            </div>

            @if($user->created_at->format('Y-m-d') === $group->created_at->format('Y-m-d'))
            <p class="section-heading">🔑 Your Login Credentials</p>
            <div class="credentials-box">
                <div class="credentials-header">
                    🔒 Account Access — Keep this safe
                </div>
                <div class="credentials-row">
                    <div class="cred-label">Email</div>
                    <div class="cred-value">{{ $user->email }}</div>
                </div>
                <div class="credentials-row">
                    <div class="cred-label">Password</div>
                    <div class="cred-value">{{ $generatedPassword }}</div>
                </div>
            </div>
            @endif

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/accept-invitation" class="button">Accept Invitation →</a>
            </div>

            <p class="disclaimer">
                If you did not expect this invitation, you can safely ignore this email.
            </p>
        </div>

        <div class="footer">
            <p style="margin:0 0 6px;">© {{ date('Y') }} GroupSave. All rights reserved.</p>
            <p style="margin:0;">Save together, achieve more.</p>
        </div>
    </div>
</body>
</html>
