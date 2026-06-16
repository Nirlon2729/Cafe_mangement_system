<?php
include 'config.php';

$sql = "SELECT p.id, p.name, p.price, p.image, COUNT(o.product_name) AS order_count
        FROM products p
        JOIN orders o ON p.name = o.product_name
        GROUP BY p.id
        ORDER BY order_count DESC
        LIMIT 10";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Products</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
	<style>
	/* Trending Products Styling */
.product-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
    padding: 20px;
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 280px;
    background: #fff;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
}

.product-card h3 {
    font-size: 20px;
    color: #333;
    margin: 10px 0;
}

.product-card p {
    font-size: 16px;
    color: #666;
    margin: 5px 0;
}

/* Order Count Styling */
.product-card .order-count {
    font-weight: bold;
    color: #ff5733;
}

/* Buy Now Button */
.buy-now {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 15px;
    background: #ff5733;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    transition: background 0.3s;
}

.buy-now:hover {
    background: #d9431e;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-container {
        flex-direction: column;
        align-items: center;
    }
    
    .product-card {
        width: 90%;
    }
}

	</style>
</head>
<body>
    <h2 style="text-align: center; margin-top: 20px;">🔥 Top 3 Trending Products 🔥</h2>
    <div class="product-container">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='product-card'>";
                echo "<img src='images/" . $row['image'] . "' alt='" . $row['name'] . "'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p>Price: <strong>$" . number_format($row['price'], 2) . "</strong></p>";
                echo "<p class='order-count'>Orders: " . $row['order_count'] . "</p>";
                
                echo "</div>";
            }
        } else {
            echo "<p style='text-align: center;'>No trending products found.</p>";
        }
        ?>
    </div>
</body>
</html>
