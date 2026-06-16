<?php
require 'config.php';

// Handle Insert
if (isset($_POST['add_code'])) {
    $code = trim($_POST['code']);
    $discount = intval($_POST['discount_percent']);
    $status = $_POST['status'];

    if (!empty($code) && $discount > 0) {
        $stmt = $conn->prepare("INSERT INTO discount_codes (code, discount_percent, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $code, $discount, $status);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: disc_manage.php");
    exit();
}

// Handle Update (Full Update)
if (isset($_POST['update_code'])) {
    $id = intval($_POST['id']);
    $code = trim($_POST['code']);
    $discount = intval($_POST['discount_percent']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE discount_codes SET code = ?, discount_percent = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sisi", $code, $discount, $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: disc_manage.php");
    exit();
}

// Handle Status Only Update
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE discount_codes SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: disc_manage.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM discount_codes WHERE id = $id");
    header("Location: disc_manage.php");
    exit();
}

// Fetch all discount codes
$codes = $conn->query("SELECT * FROM discount_codes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Discount Codes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }
		 header {
            background-color:black;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .home-btn {
            display: block;
            margin: 0 auto 20px auto;
            padding: 8px 16px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            width: 80px;
        }
        .card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .form-inline input,
        .form-inline select {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 120px;
            font-size: 14px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .add { background: #28a745; color: #fff; }
        .edit { background: #ffc107; color: #000; }
        .status { background: #17a2b8; color: #fff; }
        .delete { background: #dc3545; color: #fff; text-decoration: none; padding: 6px 12px; border-radius: 5px; font-size: 14px; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background: #000;
            color: #fff;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background: #f5f5f5;
        }
        td .form-inline {
            justify-content: center;
        }
		a:hover{
			color:red;
			text-decoration:underline;
		}
    </style>
</head>
<body>
<header>
<h2>Manage Discount Codes</h2>
<a href="admin.php" class="home-btn">Home</a>
</header>
<!-- Add New Code -->
<div class="card">
    <h3>Add New Code</h3>
    <form method="POST" class="form-inline">
        <input type="text" name="code" placeholder="Code" required>
        <input type="number" name="discount_percent" placeholder="%" required min="1">
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="submit" name="add_code" class="btn add">Add</button>
    </form>
</div>

<!-- List All Codes -->
<table>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Discount (%)</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $codes->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['code']); ?></td>
            <td><?= $row['discount_percent']; ?>%</td>
            <td><?= ucfirst($row['status']); ?></td>
            <td>
                <!-- Full Update Form -->
                <form method="POST" class="form-inline">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <input type="text" name="code" value="<?= htmlspecialchars($row['code']); ?>" required>
                    <input type="number" name="discount_percent" value="<?= $row['discount_percent']; ?>" required>
                    <select name="status">
                        <option value="active" <?= $row['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?= $row['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                    <button type="submit" name="update_status" class="btn status">Update Status</button>
                    <a href="disc_manage.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure?');" class="delete">Delete</a>
                </form>

               
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
