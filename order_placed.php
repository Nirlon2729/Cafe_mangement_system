<?php
session_start();
include 'config.php';  // Database connection

if (!isset($_SESSION['order_number'])) {
    echo "<p>No order placed yet.</p>";
    exit;
}

$order_number = $_SESSION['order_number'];

// Retrieve order details from the database
$sql = "SELECT * FROM orders WHERE order_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    echo "<p>Order not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - Frozen Dessert</title>
    <link rel="stylesheet" href="styles.css"> <!-- Use your existing CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        .order-details {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .order-details p {
            font-size: 16px;
            line-height: 1.6;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 25px;
            color: #fff;
            background: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Order Placed Successfully!</h1>
    <div class="order-details">
        <p><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?></p>
        <p><strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
        <p><strong>Quantity:</strong> <?= $order['product_quantity'] ?></p>
        <p><strong>Price:</strong> $<?= number_format($order['product_price'], 2) ?></p>
        <p><strong>Total:</strong> $<?= number_format($order['total_price'], 2) ?></p>
        <p><strong>Date:</strong> <?= $order['order_date'] ?></p>
    </div>

    <div class="btn-container">
        <a href="index.php" class="btn">Continue Shopping</a>
        <a href="purchase_slip.php?order_number=<?= $order['order_number'] ?>" class="btn" style="background: #2196F3;">View Receipt</a>
    </div>
</div>

</body>
</html>
