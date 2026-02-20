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
        .group-details {
            background-color: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #667eea;
        }
        .detail-value {
            color: #333;
            text-align: right;
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
        .note {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            color: #004085;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        h1,
        h2 {
            color: #2d2d2d;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        ul {
            padding-left: 20px;
            margin-bottom: 25px;
            color: #333;
        }

        li {
            margin-bottom: 10px;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 15px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: 600;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #aaa;
            font-size: 13px;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 22px;
            }

            .button {
                font-size: 15px;
                padding: 10px 18px;
            }

            th,
            td {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>You're Invited to Join a Group!</h1>

        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        <p><strong>{{ $inviter->name }}</strong> has invited you to join the savings group <strong>"{{ $group->title }}"</strong>.</p>

        <h2>Group Details</h2>
        <ul>
            <li><strong>Target Amount:</strong> £{{ number_format($group->target_amount, 2) }}</li>
            <li><strong>Monthly Contribution:</strong> £{{ number_format($group->payable_amount, 2) }}</li>
            <li><strong>Start Date:</strong> {{ $group->expected_start_date }}</li>
            <li><strong>Payment Day:</strong> {{ $group->payment_out_day }}th of each month</li>
        </ul>

        @if($user->created_at->format('Y-m-d') === $group->created_at->format('Y-m-d'))
        <h2>Your Login Credentials</h2>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Temporary Password</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $user->email }}</td>
                    <td>{{ $generatedPassword }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <p>If you did not expect this invitation, please disregard this message.</p>

        <div class="footer">
            &mdash; GroupSave Team
        </div>
    </div>

</body>

</html>