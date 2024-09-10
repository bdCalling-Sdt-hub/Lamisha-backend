<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Mail</title>
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
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Contact you information</h1>
    </div>
    <div class="content">
    <h1>First Name: {{$mailData["first_name"]}}</h1>
    <h1>Last Name:{{$mailData["last_name"]}}</h1>
    <h1>Phone: {{$mailData["phone"]}}</h1>

    <h1>Subject:{{$mailData["subject"]}}</h1>
    <h1>Email: {{$mailData["email"]}}</h1>
    <h1>Message: {{$mailData["sms"]}}</h1>
    </div>
</div>
</body>
</html>
