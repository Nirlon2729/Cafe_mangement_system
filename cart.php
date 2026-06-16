<?php
session_start();
include 'config.php'; // Database connection

// Remove item from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Calculate total price
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
	<style>
	/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f8f8;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    flex-direction: column;
}

/* Main Container */
.container {
    width: 70%;
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Cart Grid */
.cart-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0; /* No space between products */
    justify-content: center;
    margin-top: 10px;
}

/* Cart Item */
.cart-item {
    background: #fff;
    padding: 8px;
    border-radius: 8px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
    border: 1px solid #ddd; /* Slight separation */
}

/* Product Image */
.cart-item img {
    width: 90px;
    height: 90px;
    border-radius: 8px;
    object-fit: cover;
}

/* Product Name */
.product-name {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-top: 3px;
}

/* Price */
.price {
    font-size: 14px;
    color: #7d7c7c;
    font-weight: bold;
}

/* Quantity Controls */
.quantity-control button {
    padding: 4px 10px;
    border: none;
    background: black; /* Black button */
    color: white; /* White text */
    cursor: pointer;
    font-size: 16px;
    margin: 0 5px;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
}




.quantity-control input {
    width: 30px;
    text-align: center;
    font-size: 14px;
    border: 1px solid #ddd;
    padding: 3px;
}

/* Subtotal */
.subtotal {
    font-size: 14px;
    font-weight: bold;
    margin-top: 2px;
}

/* Remove Button - More Attractive & No Underline */
.remove-btn {
    background: #ff4444;
    color: white;
    padding: 8px 12px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    display: inline-block;
    text-decoration: none; /* Removes underline */
}

/* Hover Effect */
.remove-btn:hover {
    background: #cc0000;
    transform: scale(1.1);
    box-shadow: 0px 4px 8px rgba(255, 0, 0, 0.3);
}

/* Click Effect */
.remove-btn:active {
    transform: scale(0.95);
}


/* Checkout Button */
.checkout-btn {
    display: block;
    width: 40%;
    text-align: center;
    padding: 12px;
    background: #28a745;
    color: white;
    text-decoration: none;
    font-size: 16px;
    border-radius: 5px;
    margin: 10px auto;
}

.checkout-btn:hover {
    background: #218838;
}

/* Responsive */
@media (max-width: 768px) {
    .cart-grid {
        grid-template-columns: 1fr; /* One column for mobile */
    }
}
/* Header - Fixed and Full Width */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: black;
    color: white;
    text-align: center;
    padding: 20px 0; /* Bigger padding for larger appearance */
    font-size: 22px; /* Larger font size */
    font-weight: bold;
    z-index: 1000; /* Keep it on top */
}

/* Navigation Styles */
nav {
    margin-top: 10px;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 20px;
    font-size: 18px; /* Slightly bigger text */
}

nav a:hover {
    text-decoration: underline;
}

/* Ensure Content is Below the Header */
.container {
    margin-top: 100px; /* Adjust to prevent overlap */
    width: 70%;
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Cart Grid - Center Items */
.cart-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    padding: 10px;
}

/* Cart Item */
.cart-item {
    flex: 0 0 auto;
    width: 220px;
    background: #fff;
    padding: 8px;
    border-radius: 8px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
    border: 1px solid #ddd;
}


	</style>
</head>
<body>
<header>
    <h1>Ice Cream Store</h1>
    <nav>
        <a href="index.php">Home</a>
         <a href="checkout.php" >Proceed to Checkout</a>
    </nav>
</header>

<div class="container">
    <h2>Your Cart</h2>
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <div class="cart-grid">
            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                    <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="price">Rs <?php echo number_format($item['price'], 2); ?></div>
                    <div class="quantity-control">
                        <button onclick="updateQuantity(<?php echo $product_id; ?>, 'decrease')">-</button>
                        <input type="number" id="quantity-<?php echo $product_id; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="10" readonly>
                        <button onclick="updateQuantity(<?php echo $product_id; ?>, 'increase')">+</button>
                    </div>
                    <div class="subtotal">Subtotal: Rs <?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                    <a href="cart.php?remove=<?php echo $product_id; ?>" class="remove-btn">Remove</a>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="text-align: center; font-size: 18px; font-weight: bold;">Total: Rs <?php echo number_format($total_price, 2); ?></p>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    <?php endif; ?>
</div>

<script>
    function updateQuantity(productId, action) {
        var inputField = document.getElementById("quantity-" + productId);
        var quantity = parseInt(inputField.value);

        if (action === 'increase' && quantity < 10) {
            quantity++;
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        } else {
            return;
        }

        inputField.value = quantity;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                location.reload();
            }
        };
        xhr.send("product_id=" + productId + "&quantity=" + quantity);
    }
</script>
</body>
</html>
