<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Invitation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        h1 {
            color: #333333;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
        }

        ul {
            padding-left: 20px;
            margin-bottom: 20px;
            color: #333333;
        }

        li {
            margin-bottom: 8px;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #999999;
            text-align: center;
        }

        @media screen and (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 20px;
            }

            p, li {
                font-size: 15px;
            }

            .button {
                padding: 10px 16px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>You've Been Invited!</h1>

    <p>Hello {{ $user->name }},</p>

    <p><strong>{{ $inviter->name }}</strong> has invited you to join the group <strong>"{{ $group->title }}"</strong>.</p>

    <h2>Group Details:</h2>
    <ul>
        <li><strong>Target Amount:</strong> £{{ number_format($group->target_amount, 2) }}</li>
        <li><strong>Monthly Contribution:</strong> £{{ number_format($group->payable_amount, 2) }}</li>
        <li><strong>Start Date:</strong> {{ $group->expected_start_date }}</li>
        <li><strong>Payment Day:</strong> {{ $group->payment_out_day }}th of each month</li>
    </ul>

    <p>Click the button below to accept your invitation:</p>

    <a href="{{ url('/groups/accept-invitation/' . $group->id) }}" class="button">
        Accept Invitation
    </a>

    <p>If you weren’t expecting this, feel free to ignore the message.</p>

    <div class="footer">
        — GroupSave Team
    </div>
</div>

</body>
</html>
