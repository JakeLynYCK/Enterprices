<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "Inventory ID is missing.";
    exit;
}

$inventory_id = $_GET['id'];
$error = "";

// Fetch inventory item details
$query = "SELECT * FROM inventory WHERE id = :inventory_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':inventory_id', $inventory_id, PDO::PARAM_INT);
$stmt->execute();
$inventory_item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inventory_item) {
    echo "Inventory item not found.";
    exit;
}

// Fetch categories for the dropdown
$category_query = "SELECT id, name FROM categories ORDER BY name ASC";
$category_stmt = $pdo->prepare($category_query);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

// Update inventory item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product-name'];
    $category_id = !empty($_POST['category']) ? $_POST['category'] : NULL; // Allow NULL for category_id
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $update_query = "UPDATE inventory SET product_name = :product_name, category_id = :category_id, quantity = :quantity, price = :price WHERE id = :inventory_id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->bindParam(':product_name', $product_name);
    $update_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $update_stmt->bindParam(':quantity', $quantity);
    $update_stmt->bindParam(':price', $price);
    $update_stmt->bindParam(':inventory_id', $inventory_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Inventory item updated successfully.";
        header("Location: inventory.php");
        exit;
    } else {
        $error = "Failed to update inventory item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventory Item - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body>
    <main class="main-content">
        <header>
            <h1>Edit Inventory Item</h1>
        </header>

        <section class="content">
            <?php if ($error): ?>
                <p class="error-message"><?= $error ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <label for="product-name">Product Name</label>
                <input type="text" name="product-name" id="product-name" value="<?= htmlspecialchars($inventory_item['product_name']) ?>" required>

                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $inventory_item['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity">Stock Quantity</label>
                <input type="number" name="quantity" id="quantity" value="<?= htmlspecialchars($inventory_item['quantity']) ?>" required>

                <label for="price">Price</label>
                <input type="text" name="price" id="price" value="<?= htmlspecialchars($inventory_item['price']) ?>" required>

                <button type="submit" class="btn btn-primary">Update Item</button>
                <a href="inventory.php" class="btn btn-secondary">Cancel</a>
            </form>
        </section>
    </main>
</body>
</html>
