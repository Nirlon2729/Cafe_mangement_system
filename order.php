<?php
session_start();
include 'config.php'; // Database connection

// Handle status update request
if (isset($_POST['update_status'])) {
    $order_number = $_POST['order_number'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = '$new_status' WHERE order_number = '$order_number'";
    if (!$conn->query($update_sql)) {
        echo "<script>alert('Error updating status: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f6f9fc, #e9f0f7);
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 25px 10px;
            font-size: 28px;
            letter-spacing: 1px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .home-button {
            display: inline-block;
            background: linear-gradient(90deg, #ff6600, #ff3300);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 15px;
            transition: 0.3s ease;
            font-weight: bold;
        }
        .home-button:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #ff3300, #cc2900);
        }
        .order-section {
            width: 92%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 14px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: linear-gradient(90deg, #ff6600, #ff3300);
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f1f5ff;
            transition: 0.3s;
        }
        .status-select {
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            outline: none;
            transition: 0.3s;
        }
        .status-select:focus {
            border-color: #ff6600;
            box-shadow: 0 0 6px rgba(255, 102, 0, 0.4);
        }
        .update-btn {
            background: linear-gradient(90deg, #28a745, #218838);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
            font-weight: bold;
        }
        .update-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(90deg, #218838, #1c7430);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<header>
    Order History
    <br>
    <a href="admin.php" class="home-button">🏠 Back to Dashboard</a>
</header>

<section class="order-section">
    <table id="ordersTable">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Order Date</th>
                <th>Username</th>
                <th>Email</th>
                <th>Address</th>
                <th>City</th>
                <th>Products</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM orders ORDER BY order_date DESC, order_number DESC";
            $result = $conn->query($sql);

            if (!$result) {
                die("<tr><td colspan='11'>Database Error: " . $conn->error . "</td></tr>");
            }

            $orders = [];

            while ($order = $result->fetch_assoc()) {
                $order_number = htmlspecialchars($order['order_number']);

                if (empty($order['product_name']) || empty($order['product_quantity']) || !isset($order['product_price'])) {
                    continue;
                }

                if (!isset($orders[$order_number])) {
                    $orders[$order_number] = [
                        'order_date' => htmlspecialchars($order['order_date']),
                        'username' => htmlspecialchars($order['username']),
                        'email' => htmlspecialchars($order['email']),
                        'address' => htmlspecialchars($order['address']),
                        'city' => htmlspecialchars($order['city']),
                        'total_price' => number_format((float)$order['total_price'], 2),
                        'payment_method' => htmlspecialchars($order['payment_method']),
                        'status' => htmlspecialchars($order['status']),
                        'products' => []
                    ];
                }

                $product_details = "{$order['product_quantity']}x {$order['product_name']} (Rs " . number_format($order['product_price'], 2) . ")";
                $orders[$order_number]['products'][] = $product_details;
            }

            foreach ($orders as $order_number => $order) {
                $statusBadge = $order['status'] == "Completed" 
                    ? "<span class='status-badge status-completed'>Completed</span>" 
                    : "<span class='status-badge status-pending'>Pending</span>";

                echo "<tr>
                        <td>{$order_number}</td>
                        <td>{$order['order_date']}</td>
                        <td>{$order['username']}</td>
                        <td>{$order['email']}</td>
                        <td>{$order['address']}</td>
                        <td>{$order['city']}</td>
                        <td>" . implode('<br>', $order['products']) . "</td>
                        <td>Rs {$order['total_price']}</td>
                        <td>{$order['payment_method']}</td>
                        <td>{$statusBadge}</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='order_number' value='{$order_number}'>
                                <select name='status' class='status-select'>
                                    <option value='Pending' " . ($order['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                    <option value='Completed' " . ($order['status'] == 'Completed' ? 'selected' : '') . ">Completed</option>
                                </select>
                                <button type='submit' name='update_status' class='update-btn'>Update</button>
                            </form>
                        </td>
                      </tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</section>
</body>
</html>
