<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ - Café Online Store</title>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      margin: 0;
      padding: 0;
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
      margin-top: 5px;
      font-size: 16px;
      color: #ddd;
    }
    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    }
    .faq {
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
    }
    .faq h3 {
      font-size: 18px;
      cursor: pointer;
      position: relative;
      padding-right: 25px;
    }
    .faq h3::after {
      content: "+";
      position: absolute;
      right: 0;
      top: 0;
      font-size: 20px;
      color: #ff914d;
      transition: transform 0.3s;
    }
    .faq.active h3::after {
      content: "-";
      transform: rotate(180deg);
    }
    .faq p {
      display: none;
      margin-top: 10px;
      color: #555;
    }
    .faq.active p {
      display: block;
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
    <h1>Frequently Asked Questions</h1>
    <p>Quick answers to common questions</p>
	<a href="index.php">home</a>
  </header>

  <div class="container">

    <div class="faq">
      <h3>How do I place an order?</h3>
      <p>You can browse our menu, add items to your cart, and checkout using our secure payment options.</p>
    </div>

    <div class="faq">
      <h3>What payment methods do you accept?</h3>
      <p>We accept credit/debit cards, UPI, net banking, and cash on delivery (if available in your area).</p>
    </div>

    <div class="faq">
      <h3>Do you offer home delivery?</h3>
      <p>Yes, we provide fast and reliable delivery services. Delivery times may vary based on location.</p>
    </div>

    <div class="faq">
      <h3>Can I cancel or change my order?</h3>
      <p>Yes, you can cancel or change your order within 15 minutes of placing it by contacting our support team.</p>
    </div>

    

    <div class="faq">
      <h3>How can I contact customer support?</h3>
      <p>You can reach us via email at [support@cafemanagement.com] or call us at [11111 11111]. or rease a complain through contect page</p>
    </div>

  </div>

  <footer>
    <p>&copy; 2025 [Your Café Name]. All rights reserved.</p>
  </footer>

  <script>
    const faqs = document.querySelectorAll(".faq");
    faqs.forEach(faq => {
      faq.addEventListener("click", () => {
        faq.classList.toggle("active");
      });
    });
  </script>

</body>
</html>
