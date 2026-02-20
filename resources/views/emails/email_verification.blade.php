<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Verify Your Email Address</title>
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
                                Verify Your Email Address ✉️
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
                                Hi {{ $name }},
                            </p>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                We need to verify your email address to activate your account or process your changes. This helps us keep your GroupSave account secure.
                            </p>

                            <p style="
                    margin: 0 0 24px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                    font-weight: 600;
                  ">
                                Use the following code to verify your email:
                            </p>

                            <!-- Verification Code Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 24px">
                                <tr>
                                    <td align="center" style="
                        padding: 24px;
                        background-color: #0d4d4d;
                        border: 2px solid #4dd0e1;
                        border-radius: 8px;
                      ">
                                        <p style="
                          margin: 0;
                          font-size: 14px;
                          color: #b2dfdb;
                          line-height: 1.5;
                          margin-bottom: 10px;
                        ">
                                            Verification Code:
                                        </p>
                                        <p style="
                          margin: 0;
                          font-size: 28px;
                          font-weight: bold;
                          color: #4dd0e1;
                          letter-spacing: 2px;
                          font-family: 'Courier New', monospace;
                        ">
                                            {{ $verificationCode }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                    margin: 0 0 16px;
                    font-size: 15px;
                    color: #b2dfdb;
                    line-height: 1.6;
                  ">
                                <span style="color: #4dd0e1; font-weight: 600">⏱️ Important:</span>
                                This code will expire in 15 minutes. If you did not request email verification, please ignore this email and your account will remain unchanged.
                            </p>

                            <!-- Security Information -->
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
                                            <span style="color: #4dd0e1; font-weight: 600">🔒 Security Tips:</span>
                                        </p>
                                        <p style="
                          margin: 8px 0 0;
                          font-size: 14px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            • Never share this code with anyone
                                        </p>
                                        <p style="
                          margin: 4px 0 0;
                          font-size: 14px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            • GroupSave staff will never ask for your verification code
                                        </p>
                                        <p style="
                          margin: 4px 0 0;
                          font-size: 14px;
                          color: #b2dfdb;
                          line-height: 1.5;
                        ">
                                            • Only use codes from official GroupSave emails
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                    margin: 0 0 28px;
                    font-size: 16px;
                    color: #e0f2f1;
                    line-height: 1.6;
                  ">
                                <strong style="color: #4dd0e1">Next step:</strong> Enter this code in your GroupSave app or click the button below to verify your email.
                            </p>

                            <!-- Verify Email Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                style="margin-bottom: 32px">
                                <tr>
                                    <td align="center" style="padding: 0">
                                        <a href="{{ url('/verify-email?code=' . $verificationCode) }}" style="
                          display: inline-block;
                          padding: 16px 40px;
                          font-size: 16px;
                          font-weight: 600;
                          color: #0d4d4d;
                          background-color: #4dd0e1;
                          text-decoration: none;
                          border-radius: 8px;
                        ">Verify Email</a>
                                    </td>
                                </tr>
                            </table>

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
                                You are receiving this email because email verification was requested for your GroupSave account. If you did not make this request, please disregard this email. Your account will remain unchanged unless you verify this email.
                            </p>
                            <p style="margin: 0; font-size: 12px">
                                <a href="#" style="
                      color: #4dd0e1;
                      text-decoration: none;
                      margin-right: 10px;
                    ">Privacy policy</a>
                                <a href="#" style="color: #4dd0e1; text-decoration: none">Terms of
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
