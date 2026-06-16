<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); // Set your timezone

// Ensure order ID is passed
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    die("Invalid request. Order ID is missing.");
}

$order_id = $_GET['order_id'];

// Read orders.json file
$ordersFile = 'orders.json';
if (!file_exists($ordersFile) || filesize($ordersFile) == 0) {
    die("No orders found.");
}

$orders = json_decode(file_get_contents($ordersFile), true);
if ($orders === null) {
    die("Error reading order data.");
}

// Find the correct order
$order = null;
foreach ($orders as $o) {
    if (isset($o['order_number']) && $o['order_number'] === $order_id) {
        $order = $o;
        break;
    }
}

if (!$order) {
    die("Order not found.");
}

// Use server time instead of stored order time
$order_date = date("d M Y, h:i A", time()); // Current server time
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Slip</title>
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
    text-align: center;
}

.container {
    max-width: 400px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin: auto;
    text-align: left;
    border: 2px solid black;
}

h2 {
    text-align: center;
    color: black;
    text-transform: uppercase;
    font-size: 20px;
    margin-bottom: 10px;
    border-bottom: 2px dashed black;
    padding-bottom: 10px;
}

p {
    font-size: 14px;
    margin: 5px 0;
}

h3 {
    font-size: 16px;
    color: black;
    text-decoration: underline;
    margin-top: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 14px;
}

table, th, td {
    border: 1px solid black;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background: #ddd;
    font-weight: bold;
    text-align: center;
}

.total {
    font-weight: bold;
    text-align: right;
    margin-top: 10px;
}

.thank-you {
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    margin-top: 15px;
}

.quotes {
    font-size: 12px;
    font-style: italic;
    text-align: center;
    color: #555;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px dashed black;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

.btn {
    padding: 8px 16px;
    font-size: 14px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
}

.print-btn {
    background: #4CAF50;
    color: white;
}

.print-btn:hover {
    background: #45a049;
}

.cancel-btn {
    background: #d9534f;
    color: white;
    margin-left: 10px;
}

.cancel-btn:hover {
    background: #c9302c;
}

    </style>
</head>
<body>

<div class="container">
    <h2>Purchase Slip</h2>
    <p><strong>Order Number:</strong> #<?php echo htmlspecialchars($order['order_number']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($order_date); ?></p>

    <h3>Customer Details</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>

    <h3>Order Details</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price (Rs)</th>
            <th>Total (Rs)</th>
        </tr>
        <?php foreach ($order['items'] as $item) : ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo number_format($item['total'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p><strong>Total Amount: Rs <?php echo number_format($order['total_price'], 2); ?></strong></p>

   <div class="thank-you">
    <p>Thank you for your purchase! We hope our desserts bring joy to your day.</p>
</div>

<div class="quotes">
    <p>"Life is short, eat the dessert first!" - Jacques Torres</p>
    <p>"There is no love sincerer than the love of sweets." - George Bernard Shaw</p>
</div>


    <div class="button-container">
        <button class="btn print-btn" onclick="window.print()">Print Receipt</button>
        <a href="index.php" class="btn cancel-btn">Cancel</a>
    </div>
</div>

</body>
</html>
