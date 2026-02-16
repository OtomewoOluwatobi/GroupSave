<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Welcome to GroupSave</title>
</head>

<body style="
      margin: 0;
      padding: 0;
      background-color: #0d4d4d;
      font-family: Arial, Helvetica, sans-serif;
    ">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
        style="background-color: #0d4d4d">
        <tr>
            <td align="center" style="padding: 40px 20px">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0"
                    style="max-width: 600px; background-color: #0f5959">
                    <!-- Header - Logo -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 30px">
                            <h2 style="
                    margin: 0;
                    font-size: 28px;
                    font-weight: bold;
                    color: #4dd0e1;
                    letter-spacing: 1px;
                  ">
                                GroupSave
                            </h2>
                        </td>
                    </tr>

                    <!-- Main Body - Greeting -->
                    <tr>
                        <td style="padding: 40px 40px 0">
                            <h1 style="
                    margin: 0;
                    font-size: 24px;
                    font-weight: bold;
                    color: #ffffff;
                    line-height: 1.3;
                  ">
                                Welcome to GroupSave! 🎉
                            </h1>
                        </td>
                    </tr>

                    <!-- Main Body - Content -->
                    <tr>
                        <td style="padding: 24px 40px 0">
                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                Hi {{ $user->name }},
                            </p>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                Congratulations — you're now registered as a
                                <strong style="color: #ffffff">Coordinator</strong> on
                                GroupSave.
                            </p>

                            <p style="
                    margin: 0 0 16px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    font-weight: 600;
                  ">
                                You can now:
                            </p>

                            <!-- What You Can Do List -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 28px">
                                <tr>
                                    <td style="padding: 0 0 0 20px">
                                        <p style="
                          margin: 0 0 10px;
                          font-size: 15px;
                          color: #e0f2f1;
                          line-height: 1.6;
                        ">
                                            • Create GroupSave circles
                                        </p>
                                        <p style="
                          margin: 0 0 10px;
                          font-size: 15px;
                          color: #e0f2f1;
                          line-height: 1.6;
                        ">
                                            • Set contribution amounts and frequency
                                        </p>
                                        <p style="
                          margin: 0 0 10px;
                          font-size: 15px;
                          color: #e0f2f1;
                          line-height: 1.6;
                        ">
                                            • Invite members
                                        </p>
                                        <p style="
                          margin: 0 0 10px;
                          font-size: 15px;
                          color: #e0f2f1;
                          line-height: 1.6;
                        ">
                                            • Mark contributions as received
                                        </p>
                                        <p style="
                          margin: 0 0 0;
                          font-size: 15px;
                          color: #e0f2f1;
                          line-height: 1.6;
                        ">
                                            • Manage rotation schedules
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                    margin: 0 0 16px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    font-weight: 600;
                  ">
                                To maintain trust and transparency:
                            </p>

                            <!-- Trust & Transparency Steps -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 12px">
                                <tr>
                                    <td style="
                        padding: 16px;
                        background-color: #0d4d4d;
                        border-radius: 8px;
                      ">
                                        <p style="
                          margin: 0;
                          font-size: 15px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            <span style="color: #4dd0e1; font-weight: 600">1.</span>
                                            Only mark a contribution as "Received" once you've
                                            verified payment outside the app.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 12px">
                                <tr>
                                    <td style="
                        padding: 16px;
                        background-color: #0d4d4d;
                        border-radius: 8px;
                      ">
                                        <p style="
                          margin: 0;
                          font-size: 15px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            <span style="color: #4dd0e1; font-weight: 600">2.</span>
                                            Members must confirm the entry before it becomes locked
                                            in the ledger.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 24px">
                                <tr>
                                    <td style="
                        padding: 16px;
                        background-color: #0d4d4d;
                        border-radius: 8px;
                      ">
                                        <p style="
                          margin: 0;
                          font-size: 15px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            <span style="color: #4dd0e1; font-weight: 600">3.</span>
                                            Once confirmed, records cannot be edited or deleted.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 15px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    font-style: italic;
                    padding: 16px;
                    background-color: #0d4d4d;
                    border-left: 4px solid #4dd0e1;
                    border-radius: 4px;
                  ">
                                This ensures fairness and protects both you and your members.
                            </p>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    font-weight: 600;
                  ">
                                Remember:
                                <span style="color: #ffffff">GroupSave does not hold funds.</span>
                                It exists purely as a shared, immutable record for your group.
                            </p>

                            <p style="
                    margin: 0 0 28px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                <strong style="color: #4dd0e1">Your next step:</strong> Log in
                                and create your first group to begin inviting members.
                            </p>

                            <!-- Get Started Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 32px">
                                <tr>
                                    <td align="center" style="padding: 0">
                                        <a href="{{ $user->verifyLink }}" style="
                          display: inline-block;
                          padding: 16px 40px;
                          font-size: 16px;
                          font-weight: 600;
                          color: #0d4d4d;
                          background-color: #4dd0e1;
                          text-decoration: none;
                          border-radius: 8px;
                        ">Log In & Create Your First Group</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                We're excited to support you in running a smooth, dispute-free
                                GroupSave cycle.
                            </p>

                            <p style="
                    margin: 0;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    padding-bottom: 30px;
                  ">
                                Warm regards,<br />
                                <span style="color: #4dd0e1; font-weight: 500">The GroupSave Team</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Social Media Section -->
                    <tr>
                        <td style="
                  padding: 20px 40px;
                  border-top: 1px solid #1a7373;
                  border-bottom: 1px solid #1a7373;
                ">
                            <p style="margin: 0; text-align: center; font-size: 14px">
                                <a href="https://www.instagram.com/groupsave" target="_blank" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin: 0 12px;
                    ">Instagram</a>
                                <span style="color: #80cbc4">|</span>
                                <a href="https://web.facebook.com/groupsave" target="_blank" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin: 0 12px;
                    ">Facebook</a>
                                <span style="color: #80cbc4">|</span>
                                <a href="https://x.com/groupsave" target="_blank" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin: 0 12px;
                    ">X</a>
                                <span style="color: #80cbc4">|</span>
                                <a href="https://www.youtube.com/@groupsave" target="_blank" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin: 0 12px;
                    ">YouTube</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Copyright -->
                    <tr>
                        <td align="center" style="padding: 0 40px 20px">
                            <p style="
                    margin: 0;
                    font-size: 12px;
                    color: #80cbc4;
                    margin-top: 30px;
                  ">
                                ©️ 2026 GroupSave. All rights reserved.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer Legal -->
                    <tr>
                        <td align="center" style="padding: 0 40px 40px">
                            <p style="
                    margin: 0 0 16px;
                    font-size: 12px;
                    color: #80cbc4;
                    line-height: 1.6;
                    max-width: 500px;
                  ">
                                You are receiving this mail because you registered to join
                                GroupSave as a Coordinator. This also shows that you agree to
                                our Terms of use and Privacy Policies. If you no longer want
                                to receive mails from us, click the unsubscribe link below to
                                unsubscribe.
                            </p>
                            <p style="margin: 0; font-size: 12px">
                                <a href="{{ $privacyPolicyLink }}" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin-right: 10px;
                    ">Privacy policy</a>
                                <a href="{{ $termsLink }}" style="color: #4dd0e1; text-decoration: none">Terms of
                                    service</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
