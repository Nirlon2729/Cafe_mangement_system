<?php
session_start();

// If total price not sent, redirect to index
if (!isset($_POST['total_price'])) {
    unset($_SESSION['cart']);
    header("Location: index.php");
    exit();
}

$total_price = $_POST['total_price'];
$original_price = isset($_POST['original_price']) ? $_POST['original_price'] : $total_price;
$discount_code = isset($_POST['discount_code']) ? $_POST['discount_code'] : "";

// Cancel logic
if (isset($_POST['cancel_order'])) {
    unset($_SESSION['cart']);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .payment-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            border: 2px solid black;
        }

        h1, h2, p {
            text-align: center;
            margin: 10px 0;
        }

        .input-field, select {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            border-radius: 8px;
            border: 1px solid black;
            font-size: 16px;
            background: #f9f9f9;
        }

        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .pay-btn {
            background: #4CAF50;
            color: white;
        }

        .pay-btn:hover {
            background: #45a049;
        }

        .cancel-btn {
            background: #d9534f;
            color: white;
        }

        .cancel-btn:hover {
            background: #c9302c;
        }

        .hidden {
            display: none;
        }

        .price-info {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .discounted {
            color: green;
            font-size: 18px;
        }

        .original {
            text-decoration: line-through;
            color: red;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h1>Payment</h1>
    <h2>Complete Your Order</h2>

    <div class="price-info">
        <?php if ($original_price != $total_price): ?>
            <p class="original">Original: Rs <?php echo number_format($original_price, 2); ?></p>
            <p class="discounted">Discounted: Rs <?php echo number_format($total_price, 2); ?></p>
        <?php else: ?>
            <p>Total: Rs <?php echo number_format($total_price, 2); ?></p>
        <?php endif; ?>
    </div>

    <form method="POST" action="complete_checkout.php">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
        <input type="hidden" name="original_price" value="<?php echo $original_price; ?>">
        <input type="hidden" name="discount_code" value="<?php echo htmlspecialchars($discount_code); ?>">

        <label for="payment_method">Select Payment Method:</label>
        <select name="payment_method" id="payment_method" class="input-field" required onchange="togglePaymentDetails()">
            <option value="">-- Select --</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Paytm" selected>Paytm</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="COD">Cash on Delivery</option>
        </select>

        <!-- Credit/Debit Card -->
        <div id="card_details" class="hidden">
            <input type="text" name="card_number" class="input-field" placeholder="Card Number">
            <input type="text" name="expiry_date" class="input-field" placeholder="MM/YY">
            <input type="text" name="cvv" class="input-field" placeholder="CVV">
        </div>

        <!-- Paytm -->
        <div id="paytm_details" class="hidden">
            <input type="text" name="paytm_id" class="input-field" placeholder="Paytm ID">
        </div>

        <!-- Bank Transfer -->
        <div id="bank_details" class="hidden">
            <input type="text" name="bank_account" class="input-field" placeholder="Account Number">
            <input type="text" name="bank_ifsc" class="input-field" placeholder="IFSC Code">
        </div>

        <button type="submit" name="pay_now" class="btn pay-btn">Pay Now</button>
    </form>

    <form method="POST">
        <button type="submit" name="cancel_order" class="btn cancel-btn">Cancel Order</button>
    </form>
</div>

<script>
    function togglePaymentDetails() {
        var method = document.getElementById("payment_method").value;
        document.getElementById("card_details").style.display = (method === "Credit Card" || method === "Debit Card") ? "block" : "none";
        document.getElementById("paytm_details").style.display = (method === "Paytm") ? "block" : "none";
        document.getElementById("bank_details").style.display = (method === "Bank Transfer") ? "block" : "none";
    }

    document.addEventListener("DOMContentLoaded", togglePaymentDetails);
</script>

</body>
</html>
