<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Café Online Store</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", Arial, sans-serif;
      line-height: 1.6;
      background: #fafafa;
      color: #333;
    }
    header {
      background: #000;
      color: #fff;
      text-align: center;
      padding: 30px 20px;
    }
    header h1 {
      margin: 0;
      font-size: 32px;
    }
    header p {
      font-size: 16px;
      margin-top: 5px;
      color: #ddd;
    }
    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    }
    h2 {
      color: #444;
      margin-top: 20px;
      border-left: 4px solid #ff914d;
      padding-left: 10px;
    }
    p {
      margin: 10px 0;
    }
    .team {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .team-member {
      background: #f7f7f7;
      padding: 15px;
      border-radius: 10px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    .team-member:hover {
      transform: scale(1.05);
    }
    .team-member img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }
    footer {
      background: #000;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <h1>About Us</h1>
    <p>Discover the story behind our café ☕</p>
		<a href="index.php">home</a>

  </header>

  <div class="container">
    <h2>Our Story</h2>
    <p>
      Welcome to <strong>[Cafe management]</strong> – your go-to online café for delicious coffee, snacks, and desserts.  
      What started as a small local café has now grown into an online store, allowing coffee lovers like you to enjoy our
      freshly brewed drinks and handmade treats from the comfort of your home.
    </p>

    <h2>Our Mission</h2>
    <p>
      We believe that coffee is more than just a drink – it’s an experience. Our mission is to serve premium quality 
      coffee and snacks made with love, passion, and the finest ingredients.  
      Whether it’s your morning boost or a relaxing evening sip, we aim to make every cup memorable.
    </p>

    <h2>Why Choose Us?</h2>
    <ul>
      <li>Freshly brewed coffee, roasted to perfection.</li>
      <li>Handmade desserts and café-style snacks.</li>
      <li>Fast and reliable online delivery.</li>
      <li>A commitment to quality, taste, and sustainability.</li>
    </ul>

    <h2>Meet Our Team</h2>
    <div class="team">
      <div class="team-member">
        <img src="team1.jpg" alt="Founder">
        <h3>[Founder’s Name]</h3>
        <p>Founder & Head Barista</p>
      </div>
      <div class="team-member">
        <img src="team2.jpg" alt="Chef">
        <h3>[Chef’s Name]</h3>
        <p>Pastry Chef</p>
      </div>
      <div class="team-member">
        <img src="team3.jpg" alt="Manager">
        <h3>[Manager’s Name]</h3>
        <p>Operations Manager</p>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 [Cafe management]. All rights reserved.</p>
  </footer>

</body>
</html>
