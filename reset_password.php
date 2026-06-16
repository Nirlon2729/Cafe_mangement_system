<?php
session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php?error=Please login first.");
    exit();
}

$email = $_SESSION['email'];
$message = "";
$message_class = ""; // Class for styling messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from the database
    $query = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    if (!$db_password || $current_password !== $db_password) {
        $message = "Incorrect current password!";
        $message_class = "error-message";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $message_class = "error-message";
    } else {
        // Update new password
        $update_query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
            $message = "Password updated successfully!";
            $message_class = "success-message";
        } else {
            $message = "Error updating password.";
            $message_class = "error-message";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #2C5364, #203A43, #0F2027);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: 0.3s;
        }
        .container:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #333;
            font-weight: 600;
        }
        .email-display {
            font-weight: bold;
            color: #555;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        input:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background: #007bff;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }
        .success-message {
            color: green;
            background: #e9f6e9;
            border: 1px solid green;
        }
        .error-message {
            color: red;
            background: #f8d7da;
            border: 1px solid red;
        }
        .home-button {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .home-button:hover {
            background: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <p class="email-display">Logged in as: <?php echo htmlspecialchars($email); ?></p>
    <form method="POST">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php if (!empty($message)) : ?>
        <p class="message <?php echo $message_class; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <a href="index.php" class="home-button">Back to Home</a>
</div>

</body>
</html>
