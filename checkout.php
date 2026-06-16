<?php
session_start();
require 'config.php';

// Redirect if user not logged in
if (!isset($_SESSION['username'], $_SESSION['email'], $_SESSION['address'], $_SESSION['city'])) {
    header("Location: login.php?error=Please login first.");
    exit();
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$address = $_SESSION['address'];
$city = $_SESSION['city'];

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$total_price = 0;

// Handle discount code
$discount_code = isset($_POST['discount_code']) ? trim($_POST['discount_code']) : '';
$discount_percent = 0;
$discount_error = '';

if (!empty($discount_code)) {
    $stmt = $conn->prepare("SELECT discount_percent FROM discount_codes WHERE code = ? AND status = 'active'");
    $stmt->bind_param("s", $discount_code);
    $stmt->execute();
    $stmt->bind_result($discount_percent);
    if (!$stmt->fetch()) {
        $discount_percent = 0;
        $discount_error = "Invalid or inactive code.";
    }
    $stmt->close();
}

// Handle user info update
$update_msg = '';
if (isset($_POST['update_user'])) {
    $new_username = trim($_POST['username']);
    $new_address = trim($_POST['address']);
    $new_city = trim($_POST['city']);

    $update_query = "UPDATE users SET username=?, address=?, city=? WHERE email=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssss", $new_username, $new_address, $new_city, $email);
    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $_SESSION['address'] = $new_address;
        $_SESSION['city'] = $new_city;
        $update_msg = "✅ Details updated successfully!";
    } else {
        $update_msg = "❌ Error updating details.";
    }
    $stmt->close();
}

// Get product details
$product_details = array();
if (!empty($cart_items)) {
    $product_ids = implode(',', array_map('intval', array_keys($cart_items)));
    $query = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $product_details[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Elegant Checkout</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #eef5ff, #ffffff);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeIn 0.7s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
        header {
            background: #0078d4;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1.6em;
            letter-spacing: 0.5px;
        }
        section {
            padding: 30px 40px;
            border-bottom: 1px solid #e0e0e0;
        }
        section:last-child { border-bottom: none; }
        .product {
            display: flex;
            align-items: center;
            background: #f9fbff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .product:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.1);
        }
        .product img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 20px;
            border: 1px solid #ccc;
        }
        .product h4 {
            margin: 0;
            font-size: 18px;
            color: #222;
        }
        .price-line {
            margin-top: 6px;
            color: #555;
            font-size: 15px;
        }
        .total, .discount {
            text-align: right;
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
        }
        .code-form {
            text-align: right;
            margin-top: 20px;
        }
        input[type="text"], input[type="email"] {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 250px;
            font-size: 15px;
            transition: 0.3s;
        }
        input:focus {
            border-color: #0078d4;
            box-shadow: 0 0 5px rgba(0,120,212,0.2);
            outline: none;
        }
        button {
            background: #0078d4;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        button:hover {
            background: #005fa3;
        }
        .error { color: red; font-size: 14px; margin-top: 5px; text-align: right; }
        .marquee {
            background: #fff8e5;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            color: #444;
            margin-top: 15px;
            text-align: center;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
        }
        .user-section {
            background: #f7faff;
            padding: 30px 40px;
            border-radius: 0 0 16px 16px;
        }
        .user-section h3 {
            color: #0078d4;
            font-size: 20px;
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .form-group label {
            flex: 1;
            font-weight: 500;
            color: #333;
        }
        .form-group input {
            flex: 2;
        }
        .checkbox-group {
            text-align: center;
            margin-top: 15px;
        }
        .update-msg {
            text-align: center;
            font-weight: 500;
            margin-top: 10px;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <header>Checkout</header>

    <!-- Product Section -->
    <section>
        <?php if (!empty($cart_items)) : ?>
            <?php foreach ($cart_items as $id => $item) :
                $product = isset($product_details[$id]) ? $product_details[$id] : null;
                if (!$product) continue;
                $subtotal = $product['price'] * $item['quantity'];
                $total_price += $subtotal;
            ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div>
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p class="price-line">Price: Rs <?php echo number_format($product['price'], 2); ?> | Qty: <?php echo $item['quantity']; ?></p>
                        <p class="price-line">Subtotal: Rs <?php echo number_format($subtotal, 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total">Original Total: Rs <?php echo number_format($total_price, 2); ?></div>

            <form method="POST" class="code-form">
                <label>Discount Code:</label>
                <input type="text" name="discount_code" placeholder="Enter code" value="<?php echo htmlspecialchars($discount_code); ?>">
                <button type="submit">Apply</button>
            </form>

            <?php
            $marquee_text = file_exists("marquee.txt") ? file_get_contents("marquee.txt") : "Welcome to our store!";
            echo "<div class='marquee'>$marquee_text</div>";
            ?>

            <?php if ($discount_error): ?>
                <div class="error"><?php echo $discount_error; ?></div>
            <?php endif; ?>

            <?php
            $final_price = $total_price;
            if ($discount_percent > 0) {
                $discount_amount = ($discount_percent / 100.0) * $total_price;
                $final_price = $total_price - $discount_amount;
                echo '<div class="discount">Discount (' . $discount_percent . '%): Rs ' . number_format($discount_amount, 2) . '</div>';
                echo '<div class="total">Final Total: Rs ' . number_format($final_price, 2) . '</div>';
            }
            ?>

            <form action="payment.php" method="POST" style="text-align:center; margin-top:25px;">
                <input type="hidden" name="total_price" value="<?php echo $final_price; ?>">
                <input type="hidden" name="original_price" value="<?php echo $total_price; ?>">
                <input type="hidden" name="discount_code" value="<?php echo htmlspecialchars($discount_code); ?>">
                <button type="submit">Proceed to Payment</button>
            </form>
        <?php else : ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </section>

    <!-- User Info Section -->
    <section class="user-section">
        <h3>Update Your Information</h3>
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
            </div>
            <div class="form-group">
                <label>Email (cannot change):</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
            </div>
            <div class="form-group">
                <label>City:</label>
                <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>">
            </div>
            <div class="checkbox-group">
                <input type="checkbox" name="update_check" required> I confirm I want to update my details.
            </div>
            <div style="text-align:center; margin-top:20px;">
                <button type="submit" name="update_user">Update Details</button>
            </div>
        </form>
        <?php if (!empty($update_msg)): ?>
            <div class="update-msg"><?php echo $update_msg; ?></div>
        <?php endif; ?>
    </section>
</div>

</body>
</html>
