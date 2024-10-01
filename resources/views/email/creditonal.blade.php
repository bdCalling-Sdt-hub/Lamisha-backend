<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
        }
        p {
            font-size: 16px;
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            margin-top: 20px;
        }
        .footer p {
            margin: 0;
            color: #555555;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Login Credentials</h1>
        </div>
        <p>Email: {{ $email }}</p>
        <p>Password: {{ $password }}</p>

        <p> Please login and change your password</p>
    </div>
</body>
</html>
