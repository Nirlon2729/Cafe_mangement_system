<?php
session_start();
include 'config.php'; // Database connection

// Upload directory
$uploadDir = "uploads/";

// Fetch all products from the database
function fetchProducts($conn) {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Insert a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);

    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile;
        }
    }

    if ($name && $price > 0 && $image && $category) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $image, $category);
        $stmt->execute();
    }
}

// Delete a product
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = intval($_GET['delete']);

    // Delete image file too
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($imgPath);
    $stmt->fetch();
    $stmt->close();

    if ($imgPath && file_exists($imgPath)) {
        unlink($imgPath);
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header('Location: admin.php');
    exit;
}

// Update a product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $update_id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile;

            // Delete old image
            $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->bind_param("i", $update_id);
            $stmt->execute();
            $stmt->bind_result($oldImage);
            $stmt->fetch();
            $stmt->close();

            if ($oldImage && file_exists($oldImage)) {
                unlink($oldImage);
            }

            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, category = ? WHERE id = ?");
            $stmt->bind_param("sdssi", $name, $price, $image, $category, $update_id);
        }
    } else {
        // Without changing image
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ? WHERE id = ?");
        $stmt->bind_param("sdsi", $name, $price, $category, $update_id);
    }
    $stmt->execute();
    header('Location: admin.php');
    exit;
}

$products = fetchProducts($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color:black;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        /* Admin Section */
        .admin-section {
            background: white;
            padding: 25px;
            margin: 30px auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        button {
            background: #27ae60;
            color: white;
            padding: 12px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        button:hover {
            background: #219150;
        }

        /* Product List */
       .product-list {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column-reverse; /* Reverse order */
    gap: 10px; /* Space between products */
}

        .product-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .product-list img {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
        }
        .product-info {
            flex-grow: 1;
            margin-left: 15px;
        }
        .product-info h3 {
            margin: 0;
            font-size: 18px;
        }
        .product-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }

        /* Buttons */
        .delete-button, .update-button {
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            transition: 0.3s ease;
        }
        .delete-button {
            background: #e74c3c;
        }
        .delete-button:hover {
            background: #c0392b;
        }
        .update-button {
            background: #2980b9;
            margin-left: 10px;
        }
        .update-button:hover {
            background: #1c5a89;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            width: 400px;
        }
        .modal.active {
            display: block;
        }
        .modal h2 {
            text-align: center;
        }
        .modal form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .close-modal {
            background: #e74c3c;
            padding: 8px 12px;
            border: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border-radius: 6px;
            margin-top: 10px;
            transition: 0.3s;
        }
        .close-modal:hover {
            background: #c0392b;
        }
		/* General Button Styling */
button, .delete-button, .update-button {
    display: inline-block;
    padding: 12px 18px;
    font-size: 16px;
    font-weight: bold;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease-in-out;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* Add Product Button */
button[name="add_product"] {
    background: linear-gradient(135deg, #16A085, #1ABC9C);
}
button[name="add_product"]:hover {
    background: linear-gradient(135deg, #1ABC9C, #16A085);
    transform: scale(1.05);
}

/* Update Button */
.update-button {
    background: linear-gradient(135deg, #2980B9, #6DD5FA);
	margin-right: 10px; /* Adds space between update and delete buttons */
}
.update-button:hover {
    background: linear-gradient(135deg, #6DD5FA, #2980B9);
    transform: scale(1.05);
	 
}

/* Delete Button */
.delete-button {
    background: linear-gradient(135deg, #E74C3C, #FF6B6B);
	    margin-right: 10px; /* Adds space between update and delete buttons */
		text-decoration:none;
}
.delete-button:hover {
    background: linear-gradient(135deg, #FF6B6B, #E74C3C);
    transform: scale(1.05);
}

/* Modal Buttons */
.close-modal {
    background: linear-gradient(135deg, #e74c3c, #ff7675);
}
.close-modal:hover {
    background: linear-gradient(135deg, #ff7675, #e74c3c);
    transform: scale(1.05);
}

    </style>
</head>
<body>
<header>
    <h1>Admin Panel - Manage Products</h1>
    <div class="header-buttons">
        <a href="order.php" class="btn">Orders</a>
		<a href="report.php" class="btn">Order-Report</a>
		<a href="disc_manage.php" class="btn">Manage Discount</a>
		<a href="manage_marquee.php" class="btn">Manage user site</a>
        <a href="login.php" class="btn">Logout</a>
        
    </div>
</header>

<!-- Add Product Section -->
<section class="admin-section">
    <h2>Add a New Product</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" name="price" step="0.01" placeholder="Price" required>
        <input type="file" name="image" accept="image/*" required>
        <select name="category" required>
            <option value="Beverages">Beverages</option>
            <option value="Desserts">Desserts</option>
            <option value="Snacks">Snacks</option>
        </select>
        <button type="submit" name="add_product">Add Product</button>
    </form>
</section>

<!-- Manage Products Section -->
<section class="admin-section">
    <h2>Manage Products</h2>
    <ul class="product-list">
        <?php foreach ($products as $product): ?>
            <li>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                    <p>Price: Rs <?php echo number_format($product['price'], 2); ?></p>
                </div>
                <button class="update-button" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">Update</button>
                <a href="?delete=<?php echo $product['id']; ?>" class="delete-button">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<!-- Update Modal -->

<!-- Update Modal -->
<div id="update-modal" class="modal">
    <form method="post" enctype="multipart/form-data">
        <h2>Update Product</h2>
        <input type="hidden" id="product_id" name="product_id">
        <input type="text" id="update_name" name="name" required>
        <input type="number" id="update_price" name="price" step="0.01" required>

        <!-- FIXED: file input instead of text -->
        <input type="file" id="update_image" name="image" accept="image/*">

        <select id="update_category" name="category" required>
            <option value="Beverages">Beverages</option>
            <option value="Desserts">Desserts</option>
            <option value="Snacks">Snacks</option>
        </select>
        <button type="submit" name="update_product">Update Product</button>
        <button type="button" class="close-modal" onclick="closeModal()">Close</button>
    </form>
</div>


<script>

function editProduct(product) {
    document.getElementById('product_id').value = product.id;
    document.getElementById('update_name').value = product.name;
    document.getElementById('update_price').value = product.price;
    document.getElementById('update_category').value = product.category;

    // Clear file input (cannot pre-fill with old image path)
    document.getElementById('update_image').value = "";

    document.getElementById('update-modal').classList.add('active');
}

</script>
</body>
</html>
