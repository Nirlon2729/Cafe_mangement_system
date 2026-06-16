<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php?error=Please login first.");
    exit();
}

$email = $_SESSION['email'];

// Fetch all orders grouped by order_number
$stmt = $conn->prepare("SELECT * FROM orders WHERE email = ? ORDER BY order_date DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_number']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        header { background-color: black; color: white; text-align: center; padding: 20px; font-size: 24px; font-weight: bold; }
        .order-section { width: 90%; max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; }
        .order-box { background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 30px; }
        h3 { background-color: #ff6b81; color: white; padding: 10px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .status { font-weight: bold; padding: 5px; border-radius: 5px; display: inline-block; }
        .status.active { color: green; }
        .status.cancelled { color: red; }
        .cancel-btn { background-color: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; transition: background 0.3s; }
        .cancel-btn:hover { background-color: #c82333; }
        .back-home a { text-decoration: none; background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; display: inline-block; transition: background 0.3s; }
        .back-home a:hover { background-color: #45a049; }
        .message-box { padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<header>
    <h1>Your Orders</h1>
    <div class="back-home">
        <a href="index.php">Back to Home</a>
		</div>
</header>

<section class="order-section">
    <h2>Order History</h2>

    <!-- Display success or error message -->
   <?php if (isset($_SESSION['message'])): ?>
    <div class="message-box <?php echo isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success'; ?>">
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']); // Prevent errors on the next load
        ?>
    </div>
<?php endif; ?>

      

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order_number => $order_items): ?>
            <div class="order-box">
                <h3>Order Number: <?php echo htmlspecialchars($order_number); ?></h3>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_items[0]['order_date']); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order_items[0]['address']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order_items[0]['payment_method']); ?></p>
                <p>
                    <strong>Status:</strong> 
                    <span class="status <?php echo strtolower($order_items[0]['status']); ?>">
                        <?php echo htmlspecialchars($order_items[0]['status']); ?>
                    </span>
                </p>

                <table>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php 
                    $order_total = 0;
                    foreach ($order_items as $order): 
                        $order_total += $order['total_price'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo $order['product_quantity']; ?></td>
                            <td>Rs <?php echo number_format($order['product_price'], 2); ?></td>
                            <td>Rs <?php echo number_format($order['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                        <td style="font-weight: bold;">Rs <?php echo number_format($order_total, 2); ?></td>
                    </tr>
                </table>

                <?php if ($order_items[0]['status'] == 'Active'): ?>
                    <form action="cancel_order.php" method="POST">
                        <input type="hidden" name="cancel_order_number" value="<?php echo $order_number; ?>">
                        <button type="submit" class="cancel-btn">Cancel Order</button>
                    </form>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No previous orders found.</p>
    <?php endif; ?>

    <div class="back-home">
        <a href="index.php">Back to Home</a>
    </div>
</section>

</body>
</html>
