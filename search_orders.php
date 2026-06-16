<?php
session_start();
require 'config.php';

// Check login
if (!isset($_SESSION['email'])) {
    header("Location: login.php?error=Please login first.");
    exit();
}

$email = $_SESSION['email'];
$orders = array();
$message = "";

// Handle search
if (isset($_POST['search'])) {
    $search_type = $_POST['search_type'];
    $search_value = trim($_POST['search_value']);

    if ($search_value != "") {
        if ($search_type == "order_number") {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE email=? AND order_number=? ORDER BY order_date DESC");
            $stmt->bind_param("ss", $email, $search_value);
        } elseif ($search_type == "product_name") {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE email=? AND product_name LIKE ? ORDER BY order_date DESC");
            $like = "%".$search_value."%";
            $stmt->bind_param("ss", $email, $like);
        } elseif ($search_type == "order_date") {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE email=? AND DATE(order_date)=? ORDER BY order_date DESC");
            $stmt->bind_param("ss", $email, $search_value);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orders[$row['order_number']][] = $row;
        }

        if (empty($orders)) {
            $message = "No orders found for your search.";
        }
    } else {
        $message = "Please enter a value to search.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; margin:0; padding:0; }
        header { background:black; color:white; padding:15px; text-align:center; }
        .container { width:90%; max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        form { margin-bottom:20px; }
        select, input, button { padding:8px; margin-right:10px; border:1px solid #ccc; border-radius:5px; }
        button { background:#007BFF; color:white; cursor:pointer; }
        button:hover { background:#0056b3; }
        .message { margin:15px 0; font-weight:bold; color:red; }
        .order-box { border:1px solid #ddd; border-radius:8px; padding:15px; margin-bottom:20px; }
        h3 { background:#ff6b81; color:white; padding:8px; border-radius:5px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:center; }
        th { background:#f4f4f4; }
        .back-home { margin-top:20px; }
        .back-home a { background:#4CAF50; color:white; padding:10px 15px; border-radius:5px; text-decoration:none; }
        .back-home a:hover { background:#45a049; }
    </style>
</head>
<body>

<header>
    <h1>Search Orders</h1>
</header>

<div class="container">
    <form method="POST">
        <label>Search by:</label>
        <select name="search_type" required>
            <option value="order_number">Order Number</option>
            <option value="product_name">Product Name</option>
            <option value="order_date">Order Date</option>
        </select>
        <input type="text" name="search_value" placeholder="Enter value..." required>
        <button type="submit" name="search">Search</button>
    </form>

    <?php if ($message != ""): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order_number => $order_items): ?>
            <div class="order-box">
                <h3>Order Number: <?php echo htmlspecialchars($order_number); ?></h3>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($order_items[0]['order_date']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order_items[0]['status']); ?></p>

                <table>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php $total=0; foreach ($order_items as $item): $total += $item['total_price']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['product_quantity']; ?></td>
                            <td>Rs <?php echo number_format($item['product_price'],2); ?></td>
                            <td>Rs <?php echo number_format($item['total_price'],2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:bold;">Grand Total</td>
                        <td style="font-weight:bold;">Rs <?php echo number_format($total,2); ?></td>
                    </tr>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="back-home">
        <a href="customer_orders.php">Back to Orders</a>
    </div>
</div>

</body>
</html>
