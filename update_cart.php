<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Validate quantity range
    if ($quantity >= 1 && $quantity <= 10) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        echo "success";
    } else {
        echo "invalid quantity";
    }
} else {
    echo "error";
}
?>
