<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Makueni DFCS</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #6AA32D 0%, #5a8a26 100%);
        color: #ffffff;
        padding: 30px 20px;
        text-align: center;
    }

    .header h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .header p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .content {
        padding: 40px 30px;
        background-color: #ffffff;
    }

    .greeting {
        font-size: 18px;
        color: #2c5530;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .user-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
        border-left: 4px solid #6AA32D;
    }

    .user-info strong {
        color: #6AA32D;
    }

    .button-container {
        text-align: center;
        margin: 35px 0;
    }

    .button {
        display: inline-block;
        padding: 15px 30px;
        background: linear-gradient(135deg, #6AA32D 0%, #5a8a26 100%);
        color: #ffffff !important;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(106, 163, 45, 0.3);
    }

    .button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(106, 163, 45, 0.4);
    }

    .security-notice {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
        margin: 25px 0;
    }

    .security-notice strong {
        color: #7c5d00;
    }

    .footer {
        background-color: #2c5530;
        color: #ffffff;
        text-align: center;
        padding: 25px;
        font-size: 14px;
    }

    .footer p {
        margin: 5px 0;
        opacity: 0.8;
    }

    .reset-link {
        word-break: break-all;
        color: #6AA32D;
        margin: 20px 0;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #6AA32D, transparent);
        margin: 30px 0;
    }

    @media (max-width: 600px) {
        .container {
            margin: 10px;
            border-radius: 0;
        }

        .content {
            padding: 25px 20px;
        }

        .button {
            padding: 12px 25px;
            font-size: 14px;
        }
    }

    @media (prefers-color-scheme: dark) {
        .button {
            color: #ffffff !important;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset</h1>
            <p>Makueni Distributed Farmers Cooperative system</p>
        </div>

        <div class="content">
            <div class="greeting">Hello
                <!--{userName}-->,
            </div>

            <p>We received a request to reset your password for your Makueni Digital Financial Credit System account.
            </p>

            <div class="user-info">
                <strong>Account Details:</strong><br>
                👤 Name:
                <!--{userName}--><br>
                📧 Email:
                <!--{userEmail}--><br>
                🏷️ Role:
                <!--{userRole}-->
            </div>

            <p>To proceed with resetting your password, please click the button below. This will take you to a secure
                page where you can create a new password.</p>

            <div class="button-container">
                <a href="<!--{resetLink}-->" class="button">Reset My Password</a>
            </div>

            <div class="divider"></div>

            <div class="security-notice">
                <strong>⚠️ Security Notice:</strong><br>
                • If you didn't request this password reset, you can safely ignore this email<br>
                • This link will expire in 24 hours for your security<br>
                • Never share this reset link with anyone<br>
                • The DFCS team will never ask for your password via email
            </div>

            <p><strong>Alternative Access:</strong></p>
            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <div class="reset-link">
                <!--{resetLink}-->
            </div>

            <p>If you continue to experience issues, please contact our support team at
                <strong>makuenidfcs@gmail.com</strong>
            </p>

            <div class="divider"></div>

            <p style="color: #666; font-size: 14px;">
                Best regards,<br>
                <strong>Makueni DFCS Support Team</strong><br>
                Makueni Distributed Farmers Cooperative System<br>
                Makueni County
            </p>
        </div>

        <div class="footer">
            <p><strong>Makueni Distributed Farmers Cooperative System</strong></p>
            <p>Empowering Farmers • Supporting Agriculture • Building Communities</p>
            <p>&copy; 2025 Makueni County Government. All rights reserved.</p>
        </div>
    </div>
</body>

</html>