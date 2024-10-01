<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Tier Request</title>
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
        .order-details, .shipping-info, .billing-info, .payment-method {
            margin-bottom: 20px;
        }
        .order-details h2, .shipping-info h2, .billing-info h2, .payment-method h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .order-summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .order-summary th, .order-summary td {
            padding: 10px;
            border: 1px solid #dddddd;
            text-align: left;
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
        <div class="header ">
            <h1>Custom Tier Message</h1>
        </div>
        <p>Email: {{ $email }}</p>
        <p>Tier Information: {{ $trial }}</p>

    </div>







</body>
</html>
