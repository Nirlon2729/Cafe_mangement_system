<?php
session_start(); // Start the session to manage user session data

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Store the username in the session if logged in
} else {
    $username = 'Guest Login first'; // Default to guest if not logged in
}

include 'config.php'; // Database connection


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Frozen Dessert Store</title>
    <link rel="stylesheet" href="styles_home.css"> <!-- Ensure this points to your CSS -->
    <style>
        /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #fafafa;
    color: #333;
}

/* Header Styles */
header {
    background-color: black;
    color: #fff;
    padding: 20px 0;
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

header h1 {
    font-size: 2rem;
    margin: 0;
}

nav a {
    color: #fff;
    margin-left: 20px;
    text-decoration: none;
    font-size: 1rem;
    transition: color 0.3s ease, transform 0.3s ease;
}

nav a:hover {
    color: #ff6347; /* Change to an accent color on hover */
    transform: scale(1.1); /* Slightly enlarge the text for a more dynamic feel */
}

/* Hero Section */
.hero {
    background-image: url('background.png'); /* Background image */
    background-size: cover;
    background-position: center;
    padding: 80px 20px;
    text-align: center;
    color: #fff; /* Bright white text for maximum contrast */
    text-shadow: 4px 4px 10px rgba(0, 0, 0, 0.8); /* Strong shadow for enhanced readability */
}

.hero-content h2 {
    font-size: 3rem;
    margin: 0;
    color: #ffdd00; /* Bright golden yellow for high contrast */
    transition: transform 0.3s ease;
    font-weight: bold;
    text-shadow: 5px 5px 12px rgba(0, 0, 0, 1); /* Deep shadow for a glowing effect */
}

.hero-content p {
    font-size: 1.3rem;
    margin: 20px 0;
    color: #ffbb33; /* Warm, vibrant orange-yellow */
    font-weight: bold;
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.8); /* Enhances text visibility */
    transition: opacity 0.3s ease;
}

.cta-button {
    background: linear-gradient(90deg, #ff0000, #ffcc00); /* Strong red-to-gold gradient */
    color: #fff;
    padding: 14px 28px;
    text-decoration: none;
    border-radius: 30px;
    font-size: 1.2rem;
    font-weight: bold;
    margin: 15px;
    display: inline-block;
    transition: background 0.3s ease, transform 0.3s ease;
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.7); /* Deep shadow for more depth */
    border: 2px solid #fff; /* White border for contrast */
}

.cta-button:hover {
    background: linear-gradient(90deg, #ffcc00, #ff0000); /* Reverse gradient on hover */
    transform: scale(1.12); /* Slightly larger on hover */
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 1);
    border: 2px solid #ffcc00; /* Glowing gold effect */
}

/* Categories Section */
.categories {
    padding: 50px 20px;
    text-align: center;
}

.categories h2 {
    font-size: 2.5rem;
    margin-bottom: 30px;
}

.category-cards {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 5px;
}

.category-card {
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 24%; /* Slimmer card */
    margin: 5px;
    border-radius: 8px;
    text-align: center;
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.category-card h3 {
    font-size: 1.5rem;
    margin-top: 10px;
}

.category-card p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 20px;
}

.category-link {
    background-color: #a467f5;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1rem;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.category-link:hover {
    background-color: #4c00b0;
    transform: scale(1.1); /* Slightly enlarge the button on hover */
}

.category-card:hover {
    transform: scale(1.1); /* Larger hover effect */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); /* Add shadow for more emphasis */
}

.category-card img:hover {
    transform: scale(1.1); /* Zoom effect on images */
}

/* Footer Styles */
footer {
    background-color: black;
    color: white;
    text-align: center;
    padding: 40px 20px;
    font-size: 14px;
    margin-top: 50px;
}

.footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* Center all elements */
    text-align: center;
}

.footer-container div {
    flex: 1;
    min-width: 300px; /* Adjusted for better centering */
    margin: 15px;
}

.footer-container h3 {
    border-bottom: 2px solid white;
    display: inline-block;
    padding-bottom: 5px;
    font-size: 18px;
}

.footer-container ul {
    list-style: none;
    padding: 0;
}

.footer-container a {
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.footer-container a:hover {
    color: #ffcc00;
}

/* Social Media */
.social-links {
    display: flex;
    justify-content: center; /* Center social media links */
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 15px;
}

.social-links a {
    font-size: 18px;
    margin: 0 10px;
}

/* Separator Line */
.footer-separator {
    margin: 30px auto;
    width: 80%;
    border-color: #444;
}

/* Copyright */
.footer-copyright {
    margin-top: 20px;
    font-size: 14px;
}
 .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
    }
    .logo-title {
        display: flex;
        align-items: center;
    }
    .logo {
        width: 100px; /* Adjust size as needed */
        height: auto;
        margin-right: 10px;
    }
	/* Most Selling Items Section */
.most-selling {
    padding: 50px 20px;
    text-align: center;
    background-color: #fff8f0;
}

.most-selling h2 {
    font-size: 2.5rem;
    margin-bottom: 30px;
    color: #a467f5;
}

.selling-cards {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
}

.selling-card {
    background-color: #fff;
    width: 28%;
    min-width: 250px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.selling-card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: transform 0.3s ease;
}

.selling-card h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.selling-card p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
}

.selling-card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.selling-card img:hover {
    transform: scale(1.08);
}

    </style>
</head>
<body>
<header>
    <div class="container">
        <!-- Logo and Title -->
        <div class="logo-title">
            <img src="delight.png" alt="Frozen Dessert Store Logo" class="logo">
            <h1>Cafe Delight</h1>
        </div>

        <!-- Display the username (or Guest if logged out) -->
        <div class="username-greeting">
            <h4>Hello, <?php echo htmlspecialchars($username); ?>!</h4>
        </div>

        <!-- Navigation Links -->
        <nav>
            <form action="" method="get">
                <a href="customer_order.php">Orders</a>
                <a href="cart.php">View Cart</a>
                <?php if ($username === 'Guest Login first'): ?>
                    <a href="login.php">Login</a>
                <?php else: ?>
                    <a href="customer_account.php">Account</a>
                <?php endif; ?>
</form>
<script>
    function navigateToPage(url) {
        if (url) {
            window.location.href = url; // Redirect to the selected page
        }
    }
</script>
 

        </nav>
    </div>
</header>
<?php
$marquee_text = file_exists("marquee.txt") ? file_get_contents("marquee.txt") : "Welcome to our store!";
?>

<section class="discount-section" style="background-color:#ffdd00; padding:10px; margin:20px 0;">
    <marquee behavior="scroll" direction="left" style="font-size:18px; font-weight:bold; color:#000;">
        <?php echo $marquee_text; ?>
    </marquee>
</section>

<section class="hero">
    <div class="hero-content">
	
		
        <h2>Delightful Flavors in Every Sip & Bite</h2>
        <p>“Coffees, Cakes & More – Just a Click Away, Because Happiness Comes in Cups & Crumbs.”</p>
        <a href="Beverages.php" class="cta-button">Shop Beverages</a>
        <a href="Snacks.php" class="cta-button">Shop Snacks</a>
        <a href="Desserts.php" class="cta-button">Shop Desserts</a>
    </div>
</section>

<section class="categories">
    <h2>Explore Our Categories</h2>
    <div class="category-cards">
        <div class="category-card">
            <img src="coffee.jpeg" alt="Ice Creams">
            <h3>Beverages</h3>
            <p>“Sip the freshness, feel the difference.”</p>
            <a href="Beverages.php" class="category-link">Shop Now</a>
        </div>
        <div class="category-card">
            <img src="pizza.jpeg" alt="Cakes">
            <h3>Snacks</h3>
            <p>“Perfect snacks for every craving.”</p>
            <a href="Snacks.php" class="category-link">Shop Now</a>
        </div>
        <div class="category-card">
            <img src="cookies.jpeg" alt="Desserts">
            <h3>Desserts</h3>
            <p>“Desserts that melt hearts, not just mouths.”</p>
            <a href="Desserts.php" class="category-link">Shop Now</a>
        </div>
    </div>
</section>
<!-- Most Selling Items Section -->




<footer style="background-color: black; color: white; text-align: center; padding: 40px 20px; font-size: 14px; margin-top: 50px;">
    <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between; text-align: left;">
        
        <!-- About Section -->
        <div style="flex: 1; min-width: 250px; margin: 10px;">
            <h3 style="border-bottom: 2px solid white; display: inline-block; padding-bottom: 5px;">Cafe Delight Store</h3>
            <p style="line-height: 1.6;">Sweet Treats, Anytime, Anywhere! Indulge in our delicious desserts and Beverages delivered right to your doorstep.</p>
        </div>

        <!-- Quick Links -->
        <div style="flex: 1; min-width: 250px; margin: 10px;">
            <h3 style="border-bottom: 2px solid white; display: inline-block; padding-bottom: 5px;">Quick Links</h3>
            <ul style="list-style: none; padding: 0;">
                <li><a href="about.php" style="color: white; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">About Us</a></li>
                <li><a href="contact.php" style="color: white; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">Contact</a></li>
                <li><a href="faq.php" style="color: white; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">FAQs</a></li>
                <li><a href="privacy.php" style="color: white; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">Privacy Policy</a></li>
                <li><a href="terms.php" style="color: white; text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">Terms & Conditions</a></li>
            </ul>
        </div>

        <!-- Customer Support -->
        <div style="flex: 1; min-width: 250px; margin: 10px;">
            <h3 style="border-bottom: 2px solid white; display: inline-block; padding-bottom: 5px;">Customer Support</h3>
            <p><i class="fas fa-envelope"></i> Email: support@cafemanagement.com</p>
            <p><i class="fas fa-phone"></i> Phone: +91 11111 11111</p>
            <p><i class="fas fa-comments"></i> Live Chat: 9 AM - 9 PM</p>
            <p><i class="fab fa-whatsapp"></i> WhatsApp: <a href="https://wa.me/" style="color: #25D366; text-decoration: none;">Chat with us</a></p>
        </div>
    </div>

    <hr style="margin: 30px auto; width: 90%; border-color: #444;">

    <!-- Social Media & Payment Methods -->
    <div style="max-width: 1200px; margin: auto; text-align: center;">
        <h3 style="border-bottom: 2px solid white; display: inline-block; padding-bottom: 5px;">Follow Us</h3>
        <div style="margin: 15px;">
            <a href="https://www.facebook.com/nirlon.macwan.27" style="color: white; margin: 0 15px; font-size: 18px; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="https://www.instagram.com/Macwan_Nirlon" style="color: white; margin: 0 15px; font-size: 18px; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">
                <i class="fab fa-instagram"></i> Instagram
            </a>
            <a href="https://wa.me/9687007744" style="color: white; margin: 0 15px; font-size: 18px; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="https://www.youtube.com/@nirlonmacwan458" style="color: white; margin: 0 15px; font-size: 18px; transition: 0.3s;" onmouseover="this.style.color='#ffcc00'" onmouseout="this.style.color='white'">
                <i class="fab fa-youtube"></i> YouTube
            </a>
        </div>

        </div>

    <p style="margin-top: 20px;">&copy; 2025 Frozen Dessert Store | Designed by <u><i>Nirlon Macwan</i></u> | All rights reserved.</p>
</footer>

<!-- Font Awesome for Icons -->
<script src="https://kit.fontawesome.com/YOUR-KIT-CODE.js" crossorigin="anonymous"></script>




</body>
</html>
