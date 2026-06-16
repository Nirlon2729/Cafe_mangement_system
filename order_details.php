<?php
session_start();
require 'config.php'; // Database connection

// Check if order_number is provided
if (!isset($_GET['order_number'])) {
    header("Location: order_list.php?error=Invalid order.");
    exit();
}

$order_number = $_GET['order_number'];

// Fetch order details
$sql = "SELECT * FROM orders WHERE order_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $order_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No order found!</p>";
    exit();
}

// Fetch the first row to display general order details
$order = $result->fetch_assoc();
$status = $order['status'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?php echo htmlspecialchars($order_number); ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #333;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        .back-btn, .status-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .back-btn {
            background: #007bff;
        }
        .back-btn:hover {
            background: #0056b3;
        }
        .status-btn {
            background: #28a745;
        }
        .status-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Details</h2>
    <p><strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?></p>
    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?></p>
    <p><strong>Order Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order['order_date'])); ?></p>
    <p><strong>Status:</strong> <span id="order-status"><?php echo htmlspecialchars($status); ?></span></p>

    <h3>Ordered Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (Rs)</th>
                <th>Total (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_order_price = 0;
            do {
                $total_price = $order['product_quantity'] * $order['product_price'];
                $total_order_price += $total_price;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['product_quantity']); ?></td>
                    <td>Rs <?php echo number_format($order['product_price'], 2); ?></td>
                    <td><strong>Rs <?php echo number_format($total_price, 2); ?></strong></td>
                </tr>
            <?php } while ($order = $result->fetch_assoc()); ?>
        </tbody>
    </table>

    <p><strong>Grand Total:</strong> Rs <?php echo number_format($total_order_price, 2); ?></p>

    <a href="order.php" class="back-btn">Back to Orders</a>

    <?php if ($status !== 'Cancelled'): ?>
        <button id="update-status-btn" class="status-btn">Mark as Completed</button>
    <?php endif; ?>

</div>

<script>
$(document).ready(function() {
    $("#update-status-btn").click(function() {
        let orderNumber = "<?php echo $order_number; ?>";
        
        $.ajax({
            url: "update_status.php",
            type: "POST",
            data: { order_number: orderNumber },
            success: function(response) {
                if (response === "success") {
                    $("#order-status").text("Completed");
                    $("#update-status-btn").remove();
                } else {
                    alert("Failed to update status.");
                }
            }
        });
    });
});
</script>

</body>
</html>
