<?php
session_start();

// Example of adding an item to the cart
// Ensure the cart is initialized if it's not already
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to the cart (e.g., product ID)
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Check if the product is already in the cart, if so, increase the quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

// Redirect to a page after adding to the cart (e.g., homepage or cart page)
header('Location: homepage.php');
exit;
?>
