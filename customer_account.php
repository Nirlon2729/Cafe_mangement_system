<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php?error=Please login first.");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "user_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load JSON data
$json_file = 'orders.json';
$orders = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
$orders = is_array($orders) ? $orders : [];

// Get user details
$email = $_SESSION['email'];

// Fetch user data from database
$sql = "SELECT username, address, city FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Set default values if user not found
$username = isset($user['username']) ? $user['username'] : '';
$address = isset($user['address']) ? $user['address'] : '';
$city = isset($user['city']) ? $user['city'] : '';

// Handle account update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $new_username = trim($_POST['username']);
    $new_address = trim($_POST['address']);
    $new_city = trim($_POST['city']);

    // Update database
    $sql = "UPDATE users SET username = ?, address = ?, city = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $new_username, $new_address, $new_city, $email);

    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $_SESSION['address'] = $new_address;
        $_SESSION['city'] = $new_city;

        // Update JSON orders
        foreach ($orders as &$order) {
            if ($order['email'] === $email) {
                $order['username'] = $new_username;
                $order['address'] = $new_address;
                $order['city'] = $new_city;
            }
        }
        unset($order);

        file_put_contents($json_file, json_encode($orders, JSON_PRETTY_PRINT));

        header("Location: customer_account.php?success=Account updated");
        exit();
    } else {
        $message = "Error updating account.";
    }
    $stmt->close();
}

// Handle account deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM users WHERE email = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $email);

    if ($delete_stmt->execute()) {
        // Remove user from JSON orders
        $orders = array_values(array_filter($orders, function ($order) use ($email) {
            return $order['email'] !== $email;
        }));

        file_put_contents($json_file, json_encode($orders, JSON_PRETTY_PRINT));

        session_destroy();
        header("Location: logout.php?message=Account deleted successfully.");
        exit();
    } else {
        $message = "Error deleting account: " . $conn->error;
    }
    $delete_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Account</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        nav {
            margin-top: 10px;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 10px;
            padding: 8px 15px;
            border-radius: 5px;
            background: #ff5733;
            transition: background 0.3s;
        }
        nav a:hover {
            background: #c70039;
        }
        .account-section {
            max-width: 600px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .save-button, .remove-button, .logout-button {
            flex: 1;
            margin: 5px;
            padding: 12px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 16px;
			 align-items: center; /* Center horizontally */
    justify-content: center; /* Center vertically */
        }
        .save-button {
            background-color: #28a745;
            color: white;
        }
        .remove-button {
            background-color: #dc3545;
            color: white;
        }
        .logout-button {
            background-color: #007bff;
            color: white;
			text-decoration:none;
			 align-items: center; /* Center horizontally */
    justify-content: center; /* Center vertically */
        }
        .save-button:hover {
            background-color: #218838;
        }
        .remove-button:hover {
            background-color: #c82333;
        }
        .logout-button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
        }
		.reset-button {
    flex: 1;
    margin: 5px;
    padding: 12px;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    transition: 0.3s;
    font-size: 16px;
    background-color: #ffc107;
    color: black;
    text-align: center;
    display: inline-block;
    text-decoration: none;
}

.reset-button:hover {
    background-color: #e0a800;
}


    </style>
</head>
<body>
    <header>
        Customer Account
        <nav><a href="index.php">Home</a></nav>
    </header>

    <section class="account-section">
        <h2>Account Details</h2>

        <?php if (isset($_GET['success'])) : ?>
            <p class="message"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>">
            </div>

            <div class="buttons">
    <button type="submit" name="update" class="save-button">Save</button>
    <button type="submit" name="delete" class="remove-button" onclick="return confirm('Are you sure? This action cannot be undone.');">Delete</button>
   
    <a href="logout.php" class="logout-button">Logout</a>
</div>

        </form>
    </section>
</body>
</html>
