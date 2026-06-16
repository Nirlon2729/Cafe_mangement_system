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

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = trim($_POST['emp_id']);
    $emp_name = trim($_POST['emp_name']);
    $emp_password = trim($_POST['emp_password']); // Plain text password

    // Check if the employee ID already exists
    $stmt = $conn->prepare("SELECT emp_id FROM emp_data WHERE emp_id = ?");
    $stmt->bind_param('s', $emp_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Employee ID already exists. Please use a different ID.";
    } else {
        // Insert new employee into the database
        $stmt = $conn->prepare("INSERT INTO emp_data (emp_id, emp_name, emp_password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $emp_id, $emp_name, $emp_password);

        if ($stmt->execute()) {
            $success_message = "Signup successful! You can now log in.";
        } else {
            $error_message = "Error: Unable to create account. Please try again.";
        }
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
    <title>Admin Signup</title>
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
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Admin Signup</h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="emp_id">Emp_id:</label>
        <input type="text" id="emp_id" name="emp_id" required>

		<label for="emp_id">User name:</label>
        <input type="text" id="emp_id" name="emp_id" required>

		
		 <label for="emp_id">Password:</label>
        <input type="password" id="emp_id" name="emp_id" required>
		
       

        <button type="submit">Sign Up</button>
    </form>

    <p style="text-align: center;">
        Already have an account? <a href="login_admin.php">Login here</a>.
    </p>
</body>
</html>
