<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    die("Access denied.");
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_order_number'])) {
    $order_number = $_POST['cancel_order_number'];

    // Delete order from database
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_number = ? AND email = ?");
    $stmt->bind_param("ss", $order_number, $email);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order #$order_number has been cancelled successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: Unable to cancel order.";
        $_SESSION['message_type'] = "error";
    }
}

// Redirect back to orders.php
header("Location: customer_order.php");
exit();
?>
