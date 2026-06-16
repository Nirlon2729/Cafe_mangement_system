<?php
// Start server connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = "";     // Replace with your DB password
$dbname = "user_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Status message
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate inputs
    if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO customer_suggestion (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $status = "Thank you for contacting us, $name! Your message has been saved.";
        } else {
            $status = "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    } else {
        $status = "Please fill in all fields correctly.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Frozen Dessert Store</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ensure this points to your CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #444;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            height: 120px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .status {
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have questions or need assistance? We'd love to hear from you. Please fill out the form below, and we’ll get back to you as soon as possible!</p>

        <?php if (!empty($status)): ?>
            <p class="status <?php echo isset($error) ? 'error' : ''; ?>"><?php echo $status; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Write your message here" required></textarea>

            <button type="submit">Send Message</button>
			<button><a href="index.php">Go to home</a></button>
        </form>
    </div>
</body>
</html>