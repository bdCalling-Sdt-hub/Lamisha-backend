<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order confirom</title>
</head>

<body>
<ul>
    <li>First Name: {{ $input['first_name'] }}</li>
    <li>Last Name: {{ $input['last_name'] }}</li>
    <li>Email: {{ $input['email'] }}</li>
    <li>Phone: {{ $input['phone'] }}</li>
    <li>Shipping Address: {{ $input['shiping_address'] }}</li>
    <li>Item Description: {{ $input['item_description'] }}</li>
    <li>Item Number: {{ $input['item_number'] }}</li>
    <li>Price: ${{ $input['price'] }}</li>
    <li>Quantity: {{ $input['quantity'] }}</li>
    <li>Vendor: {{ $input['vendor'] }}</li>
    <li>Comments: {{ $input['comments'] }}</li>
    <li>Print Name: {{ $input['print_name'] }}</li>
</ul>

</body>

</html>