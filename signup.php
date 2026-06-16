<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmailSql);

    if ($result->num_rows > 0) {
        $error_message = "User already registered with this email.";
    } else {
        $sql = "INSERT INTO users (username, email, password, address, city) 
                VALUES ('$username', '$email', '$password', '$address', '$city')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Account created successfully! <a href='login.php'>Login here</a>";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Frozen Dessert Store</title>
    <link rel="stylesheet" href="stylesign.css">
	<style>
/* Reset basics */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

/* Body with light gradient */
body {
    background: linear-gradient(135deg, #fff6e5, #ffe9cc);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #333;
}

/* Container box */
.container {
    background: #ffffff;
    padding: 30px 35px;
    border-radius: 15px;
    box-shadow: 0px 6px 20px rgba(0,0,0,0.15);
    width: 370px;
    text-align: center;
    border: 1px solid #f2d6a2;
}

/* Headings */
.container h2 {
    margin-bottom: 20px;
    font-size: 26px;
    color: #a0522d; /* Coffee-brown accent */
    letter-spacing: 1px;
}

/* Input container */
.input-container {
    margin-bottom: 18px;
    text-align: left;
}

.input-container label {
    display: block;
    font-size: 14px;
    margin-bottom: 6px;
    color: #444;
}

.input-container input {
    width: 100%;
    padding: 12px;
    border: 1px solid #d1bfa7;
    border-radius: 8px;
    font-size: 14px;
    background: #fff9f2;
    color: #333;
    transition: 0.3s;
}

.input-container input:focus {
    border-color: #a0522d;
    outline: none;
}

/* Buttons */
button {
    width: 100%;
    padding: 12px;
    background: #a0522d; /* Coffee brown */
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #8b4513;
    transform: scale(1.03);
}

/* Links below form */
p {
    margin-top: 14px;
    font-size: 14px;
    color: #555;
}

p a {
    color: #a0522d;
    text-decoration: none;
    font-weight: bold;
}

p a:hover {
    text-decoration: underline;
}

/* Messages */
.error-message {
    background: rgba(255, 102, 102, 0.15);
    color: #cc0000;
    padding: 10px;
    margin-bottom: 14px;
    border-radius: 6px;
    font-size: 14px;
}

.success-message {
    background: rgba(102, 204, 102, 0.15);
    color: #228b22;
    padding: 10px;
    margin-bottom: 14px;
    border-radius: 6px;
    font-size: 14px;
}


	</style>
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (isset($success_message)) { ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php } ?>
        <form method="post" action="signup.php">
            <div class="input-container">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-container">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-container">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-container">
                <label>Address:</label>
                <input type="text" name="address">
            </div>
            <div class="input-container">
                <label>City:</label>
                <input type="text" name="city">
            </div>
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
