<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .success .icon::before {
            content: '✓';
            color: #10b981;
        }

        .error .icon::before {
            content: '✕';
            color: #ef4444;
        }

        .cancelled .icon::before {
            content: '○';
            color: #f59e0b;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .status-message {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .success .status-badge {
            background: #d1fae5;
            color: #059669;
        }

        .error .status-badge {
            background: #fee2e2;
            color: #dc2626;
        }

        .cancelled .status-badge {
            background: #fef3c7;
            color: #b45309;
        }

        .session-info {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #4b5563;
            word-break: break-all;
        }

        .session-label {
            font-weight: 600;
            color: #1f2937;
            display: block;
            margin-bottom: 4px;
        }

        .instructions {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            text-align: left;
            border-radius: 4px;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .success .instructions {
            background: #ecfdf5;
            border-left-color: #10b981;
            color: #065f46;
        }

        .error .instructions {
            background: #fef2f2;
            border-left-color: #ef4444;
            color: #7f1d1d;
        }

        .cancelled .instructions {
            background: #fffbeb;
            border-left-color: #f59e0b;
            color: #78350f;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .button {
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .button-primary {
            background: #667eea;
            color: white;
        }

        .button-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }

        .button-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .button-secondary:hover {
            background: #d1d5db;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 20px;
            }

            .icon {
                font-size: 48px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container {{ $status }}">
        <div class="icon"></div>
        
        <h1>{{ $title }}</h1>
        
        <div class="status-badge">
            {{ ucfirst($status) }}
        </div>

        <div class="status-message">
            {{ $message }}
        </div>

        @if($status === 'success')
            <div class="instructions">
                ✓ Your payment was processed successfully<br>
                ✓ Your new plan is being activated<br>
                ✓ You can close this window now
            </div>
        @elseif($status === 'cancelled')
            <div class="instructions">
                ○ Payment was cancelled<br>
                ○ No charge was made<br>
                ○ You can try again from the app
            </div>
        @else
            <div class="instructions">
                ✕ Payment could not be completed<br>
                ✕ Please verify your payment details<br>
                ✕ Contact support if issue persists
            </div>
        @endif

        @if($session_id)
            <div class="session-info">
                <span class="session-label">Session ID:</span>
                {{ $session_id }}
            </div>
        @endif

        <div class="button-group">
            @if($status === 'success')
                <button class="button button-primary" onclick="window.close()">
                    Close Window
                </button>
            @elseif($status === 'cancelled')
                <button class="button button-primary" onclick="window.close()">
                    Return to App
                </button>
            @else
                <button class="button button-primary" onclick="window.close()">
                    Close Window
                </button>
            @endif
        </div>

        <script>
            // Auto-close after 5 seconds for success/cancelled
            @if($status !== 'error')
            setTimeout(() => {
                window.close();
            }, 5000);
            @endif
        </script>
    </div>
</body>
</html>
