<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_number'])) {
    $order_number = $_POST['order_number'];

    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_number = ?");
    $stmt->bind_param("s", $order_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['city']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['product_quantity']); ?></td>
                <td>Rs <?php echo number_format($row['product_price'], 2); ?></td>
                <td>Rs <?php echo number_format($row['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="order_details.php?order_number=<?php echo $row['order_number']; ?>" class="btn view-btn">View</a>
                    <?php if ($row['status'] !== 'Cancelled'): ?>
                        <button class="btn complete-btn" data-order="<?php echo $row['order_number']; ?>">Complete</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile;
    } else {
        echo "<tr><td colspan='11'>No orders found with this order number.</td></tr>";
    }
}
?>
