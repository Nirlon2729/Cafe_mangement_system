<?php
session_start();
require 'config.php';

// Validate request
if (!isset($_POST['payment_method']) || !isset($_POST['total_price'])) {
    header("Location: checkout.php?error=Invalid request.");
    exit();
}

// Ensure session data exists
if (!isset($_SESSION['username']) || !isset($_SESSION['email']) || !isset($_SESSION['address']) || !isset($_SESSION['city']) || empty($_SESSION['cart'])) {
    header("Location: checkout.php?error=Session expired or cart empty.");
    exit();
}

// Collect data
$username       = $_SESSION['username'];
$email          = $_SESSION['email'];
$address        = $_SESSION['address'];
$city           = $_SESSION['city'];
$cart_items     = $_SESSION['cart'];

$original_price = isset($_POST['original_price']) ? floatval($_POST['original_price']) : floatval($_POST['total_price']);
$total_price    = floatval($_POST['total_price']);
$payment_method = $_POST['payment_method'];
$discount_code  = isset($_POST['discount_code']) ? $_POST['discount_code'] : '';
$discounted     = ($original_price > $total_price);

// Generate order number
$result = $conn->query("SELECT order_number FROM orders ORDER BY id DESC LIMIT 1");
$last_order = $result->fetch_assoc();
$last_num = $last_order ? intval(str_replace("ORD-", "", $last_order['order_number'])) : 0;
$order_number = "ORD-" . ($last_num + 1);

// Save each cart item
foreach ($cart_items as $item) {
    $product_name     = $item['name'];
    $product_quantity = $item['quantity'];
    $product_price    = $item['price'];

    $stmt = $conn->prepare("INSERT INTO orders (order_number, username, email, address, city, product_name, product_quantity, product_price, total_price, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ssssssiids", $order_number, $username, $email, $address, $city, $product_name, $product_quantity, $product_price, $total_price, $payment_method);
    $stmt->execute();
}

// Clear cart
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 30px;
        }

        .receipt-container {
            max-width: 500px;
            margin: auto;
            background: white;
            border: 2px solid black;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            border-bottom: 2px dashed black;
            padding-bottom: 10px;
        }

        p, td, th {
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
            text-align: center;
        }

        .price {
            font-weight: bold;
            margin-top: 10px;
            text-align: right;
        }

        .strikethrough {
            text-decoration: line-through;
            color: red;
        }

        .discounted {
            color: green;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 18px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            margin: 5px;
            cursor: pointer;
        }

        .print-btn {
            background-color: #28a745;
            color: white;
        }

        .home-btn {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="receipt-container" id="receipt">
    <h2>Order Receipt</h2>
    <p><strong>Order No:</strong> <?php echo htmlspecialchars($order_number); ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($username); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($address) . ', ' . htmlspecialchars($city); ?></p>
    <p><strong>Payment:</strong> <?php echo htmlspecialchars($payment_method); ?></p>

    <h3>Items:</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
        </tr>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>Rs <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p class="price">
        <?php if ($discounted): ?>
            <span class="strikethrough">Original: Rs <?php echo number_format($original_price, 2); ?></span><br>
            <span class="discounted">Discounted: Rs <?php echo number_format($total_price, 2); ?></span>
        <?php else: ?>
            <strong>Total:</strong> Rs <?php echo number_format($total_price, 2); ?>
        <?php endif; ?>
    </p>
</div>

<div class="buttons">
    <button class="btn print-btn" onclick="window.print()">Print</button>
    <a href="index.php" class="btn home-btn">Back to Home</a>
</div>

</body>
</html>
