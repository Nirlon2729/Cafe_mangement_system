<?php
session_start();
include 'config.php'; // Database connection

// Fetch all Desserts products from the database
function fetchIceCreams($conn) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = 'Desserts'");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle adding product to the cart or buying now
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Get product details
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            if (isset($_POST['buy_now'])) {
                $_SESSION['cart'] = [
                    $product_id => [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity,
                    ]
                ];
                header("Location: checkout.php");
                exit();
            } else {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity,
                    ];
                }
            }
        }
    }
}

$IceCreams = fetchIceCreams($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desserts Store</title>
    <link rel="stylesheet" href="stylespages.css">
    <script>
        function updateQuantity(action, productId) {
            var quantityInput = document.getElementById('quantity-' + productId);
            var currentQuantity = parseInt(quantityInput.value);
            if (action === 'increase' && currentQuantity < 10) {
                quantityInput.value = currentQuantity + 1;
            } else if (action === 'decrease' && currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        }
    </script>
    <style>
        .buy-now-btn {
            background-color: #ff6b81;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            display: block;
            width: 100%;
            text-align: center;
        }
        
        .buy-now-btn:hover {
            background-color: #e04a61;
        }
    </style>
</head>
<body>

<header>
    <h1>Desserts Store</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="Beverages.php">Beverages</a>
        <a href="Snacks.php">Snacks</a>
        <a href="cart.php">View Cart</a>
    </nav>
</header>

<section class="product-section">
    <h2>Our Dessertss</h2>
    <div class="product-list">
        <?php foreach ($IceCreams as $product): ?>
            <div class="product-item">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="product-image" 
                     onerror="this.src='placeholder.jpg';">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>Price: Rs <?php echo number_format($product['price'], 2); ?></p>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-controls">
                        <button type="button" onclick="updateQuantity('decrease', <?php echo $product['id']; ?>)">-</button>
                        <input type="number" name="quantity" id="quantity-<?php echo $product['id']; ?>" value="1" min="1" max="10" required>
                        <button type="button" onclick="updateQuantity('increase', <?php echo $product['id']; ?>)">+</button>
                    </div>
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                    <button type="submit" name="buy_now" class="buy-now-btn">Buy Now</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>
</html>
