<?php
include 'config.php'; // Database connection

$totalRevenueQuery = "SELECT SUM(total_price) as total FROM orders";
$totalRevenueResult = mysqli_query($conn, $totalRevenueQuery);
$totalRevenueRow = mysqli_fetch_assoc($totalRevenueResult);
$totalRevenue = $totalRevenueRow['total'];

$orders = [];
$filteredRevenue = 0;
$showRecords = false;

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $query = "SELECT * FROM orders WHERE order_date BETWEEN '{$_GET['from_date']}' AND '{$_GET['to_date']}'";
    $showRecords = true;
} elseif (!empty($_GET['month']) && !empty($_GET['year'])) {
    $query = "SELECT * FROM orders WHERE MONTH(order_date) = '{$_GET['month']}' AND YEAR(order_date) = '{$_GET['year']}'";
    $showRecords = true;
} else {
    $query = ""; // No query means no records will be shown
}

if ($showRecords && $query) {
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $orderNum = $row['order_number'];
        if (!isset($orders[$orderNum])) {
            $orders[$orderNum] = [
                'username' => $row['username'],
                'email' => $row['email'],
                'products' => [],
                'total_price' => 0,
                'order_date' => $row['order_date'],
            ];
        }
        $orders[$orderNum]['products'][] = $row['product_name'];
        $orders[$orderNum]['total_price'] += $row['total_price'];
        $filteredRevenue += $row['total_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        header {
            background-color: black;
            color: white;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
			text-align:center;
        }

        .btn {
            background: #ff9800;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #e68900;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            max-width: 900px;
            margin: 30px auto;
        }

        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 3px solid black;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            flex: 1;
            text-align: left;
        }

        input, select {
            padding: 10px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }

        button {
            background: #27ae60;
            color: white;
            padding: 12px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
            font-weight: bold;
        }

        button:hover {
            background: #219150;
        }

        .revenue-container {
            display: flex;
            justify-content: space-evenly;
            margin: 20px auto;
            max-width: 600px;
        }

        .revenue-box {
            padding: 15px;
            font-size: 1.5em;
            font-weight: bold;
            background: #e9f7ef;
            border-radius: 10px;
            width: 45%;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .table-container {
            width: 95%;
            margin: 30px auto;
            overflow-x: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            <?php if (!$showRecords) echo "display: none;"; ?>
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #222;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Order Report</h1>
        <a href="admin.php" class="btn">Back to home</a>
    </header>

    <div class="filter-container">
        <div class="filter-box">
            <form method="GET" action="">
                <label for="from_date">From Date:</label>
                <input type="date" name="from_date">
                <label for="to_date">To Date:</label>
                <input type="date" name="to_date">
                <button type="submit">Filter</button>
            </form>
        </div>
      
    </div>

    <div class="revenue-container">
        
        <div class="revenue-box">Filtered Revenue: Rs. <?php echo number_format($filteredRevenue, 2); ?></div>
		<div class="revenue-box">Total Revenue: Rs. <?php echo number_format($totalRevenue, 2); ?></div>
    </div>

    <?php if ($showRecords): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Products</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $orderNum => $order): ?>
                    <tr>
                        <td><?php echo $orderNum; ?></td>
                        <td><?php echo $order['username']; ?></td>
                        <td><?php echo $order['email']; ?></td>
                        <td><?php echo implode(', ', $order['products']); ?></td>
                        <td>Rs. <?php echo number_format($order['total_price'], 2); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</body>
</html>
