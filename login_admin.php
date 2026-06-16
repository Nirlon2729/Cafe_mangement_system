<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$database = "user_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_name = trim($_POST['emp_name']);
    $emp_password = trim($_POST['emp_password']); // Plain text password

    // Check credentials
    $stmt = $conn->prepare("SELECT emp_id FROM emp_data WHERE emp_name = ? AND emp_password = ?");
    $stmt->bind_param('ss', $emp_name, $emp_password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($emp_id);
        $stmt->fetch();

        // Store admin session
        $_SESSION['admin_id'] = $emp_id;
        $_SESSION['admin_name'] = $emp_name;

        // Redirect to admin panel
        header('Location: admin.php');
        exit;
    } else {
        $error_message = "Invalid credentials. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>User Login</h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="emp_name">Email:</label>
        <input type="text" id="emp_name" name="emp_name" required>

        <label for="emp_password">Password:</label>
        <input type="password" id="emp_password" name="emp_password" required>

        <button type="submit">Login</button>
		
    </form>
	<label align="center">Not Registered yet!?
		<a href="admin_signup.php">Sign up</a></label>
	<label align="center">login into the as admin!?
	<a href="login.php">Login</a></label>
</body>
</html>
