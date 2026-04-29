<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: #e0e0e0;
            background-color: #0d1117;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #161b22;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            border: 1px solid #30363d;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .content {
            padding: 40px 20px;
        }

        .content h2 {
            font-size: 24px;
            color: #e0e0e0;
            margin-top: 0;
        }

        .content p {
            font-size: 16px;
            margin: 15px 0;
            color: #c9d1d9;
        }

        .button {
            display: inline-block;
            background-color: #667eea;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }

        .button:hover {
            background-color: #764ba2;
        }

        .footer {
            background-color: #0d1117;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #8b949e;
            border-top: 1px solid #30363d;
        }

        .url-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #30363d;
            font-size: 12px;
            color: #8b949e;
        }

        .url-section p {
            margin: 5px 0;
        }

        code {
            background-color: #0d1117;
            padding: 2px 6px;
            border-radius: 3px;
            word-break: break-all;
            color: #79c0ff;
            border: 1px solid #30363d;
        }

        .warning {
            background-color: rgba(248, 81, 73, 0.1);
            border-left: 4px solid #f85149;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
            color: #f85149;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="content">
            <h2>Reset Your Password</h2>

            <p>You requested to reset your password. Click the button below to proceed with resetting your password.</p>

            <center>
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </center>

            <div class="warning">
                <strong>⚠️ Security Note:</strong> This link will expire in 60 minutes. If you did not request a
                password reset, you can safely ignore this email.
            </div>

            <div class="url-section">
                <p>If you're having trouble clicking the button, copy and paste this URL into your web browser:</p>
                <p><code>{{ $resetUrl }}</code></p>
            </div>
        </div>

        <div class="footer">
            <p>Regards,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
