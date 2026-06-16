<?php
include 'config.php'; // Database connection

$searchQuery = "";
$searchCondition = "";
$orders = [];

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']);
    $searchCondition = "WHERE order_number LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%'";

    $order_query = "SELECT * FROM orders $searchCondition ORDER BY order_number";
    $order_result = mysqli_query($conn, $order_query);

    while ($row = mysqli_fetch_assoc($order_result)) {
        $orderNum = $row['order_number'];
        if (!isset($orders[$orderNum])) {
            $orders[$orderNum] = [
                'username' => $row['username'],
                'email' => $row['email'],
                'address' => $row['address'],
                'payment_method' => 'Paytm', // Example, update dynamically if needed
                'products' => [],
                'total_price' => 0
            ];
        }

        $orders[$orderNum]['products'][] = [
            'name' => $row['product_name'],
            'quantity' => $row['product_quantity'],
            'price' => $row['product_price']
        ];
        $orders[$orderNum]['total_price'] += $row['total_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Purchase Slips</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
        }
        .container {
            width: 70%;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .search-box {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .search-box input {
            padding: 10px;
            width: 300px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .search-box button {
            padding: 10px 15px;
            border: none;
            background: #27ae60;
            color: white;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
            transition: 0.3s ease;
        }
        .search-box button:hover {
            background: #219150;
        }
        .receipt {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: inline-block;
            width: 100%;
            text-align: left;
        }
        .receipt h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .receipt table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .receipt th, .receipt td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .receipt th {
            background-color: #222;
            color: white;
        }
        .buttons {
            margin-top: 15px;
            text-align: center;
        }
        .print-btn {
            padding: 10px 15px;
            border: none;
            background: green;
            color: white;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        .print-btn:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Admin Purchase Slips</h1>
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Email or Order Number" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (!empty($searchQuery) && empty($orders)) { ?>
            <p>No results found.</p>
        <?php } ?>

        <?php foreach ($orders as $orderNum => $order) { ?>
        <div class="receipt" id="receipt-<?php echo $orderNum; ?>">
            <h2>Order Receipt</h2>
            <hr>
            <p><strong>Order Number:</strong> <?php echo $orderNum; ?></p>
            <p><strong>Name:</strong> <?php echo $order['username']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
            <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
            <h3>Products Ordered:</h3>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
                <?php foreach ($order['products'] as $product) { ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                </tr>
                <?php } ?>
            </table>
            <p><strong>Total:</strong> Rs. <?php echo number_format($order['total_price'], 2); ?></p>
            <div class="buttons">
                <button class="print-btn" onclick="printSlip('receipt-<?php echo $orderNum; ?>')">Print Receipt</button>
            </div>
        </div>
        <?php } ?>
    </div>

    <script>
        function printSlip(id) {
            var receipt = document.getElementById(id).cloneNode(true);
            var printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Print Receipt</title>');
            printWindow.document.write('<style>');
            printWindow.document.write(`
                body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
                .receipt { width: 60%; margin: auto; padding: 20px; border: 2px solid black; border-radius: 10px; }
                hr { border-top: 2px dashed black; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid black; padding: 8px; text-align: center; }
                th { background-color: #ddd; }
                p { font-size: 16px; }
            `);
            printWindow.document.write('</style></head><body>');
            printWindow.document.write(receipt.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
