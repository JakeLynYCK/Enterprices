<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission to add a new inventory item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Insert inventory record
    $insertQuery = "INSERT INTO inventory (product_id, quantity) VALUES (:product_id, :quantity)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':quantity', $quantity);

    if ($stmt->execute()) {
        $success_message = "Inventory item added successfully!";
    } else {
        $error_message = "Error adding inventory item.";
    }
}

// Fetch inventory items with category names, handling NULL category_id
$query = "SELECT inventory.id, inventory.product_name, inventory.quantity, inventory.price, 
          IFNULL(categories.name, 'Uncategorized') AS category_name 
          FROM inventory 
          LEFT JOIN categories ON inventory.category_id = categories.id
          ORDER BY inventory.id ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for the dropdown
$category_query = "SELECT id, name FROM categories ORDER BY name ASC";
$category_stmt = $pdo->prepare($category_query);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products from the products table
$productQuery = "SELECT id, name FROM products ORDER BY name ASC";
$productStmt = $pdo->prepare($productQuery);
$productStmt->execute();
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active"><i class="bi bi-house"></i> Dashboard</a></li>
                <li><a href="add-item.php"><i class="bi bi-plus-square"></i> Add New Item</a></li>
                <li><a href="categories.php"><i class="bi bi-folder-plus"></i> Categories</a></li>
                <li><a href="orders.php"><i class="bi bi-box-seam"></i> Orders</a></li>
                <li><a href="customers.php"><i class="bi bi-people"></i> Customers</a></li>
                <li><a href="404.html"><i class="bi bi-gift"></i> Offers</a></li>
                <li><a href="user-profile.php"><i class="bi bi-person"></i> User Profile</a></li>
                <li><a href="404.html"><i class="bi bi-building"></i> Company Profile</a></li>
                <li><a href="reports.php"><i class="bi bi-graph-up"></i> Reports</a></li>
                <li><a href="inventory.php"><i class="bi bi-archive"></i> Inventory</a></li>
                <li><a href="404.html"><i class="bi bi-search"></i> Search</a></li>
                <li><a href="feedback.php"><i class="bi bi-chat-left-text"></i> Feedback</a></li>
                <li><a href="payments.php"><i class="bi bi-credit-card"></i> Payments</a></li>
                <li><a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1>Inventory Management</h1>
        </header>

        <section class="content">
            <?php if (isset($_SESSION['success'])): ?>
                <p class="success-message"><?= $_SESSION['success'] ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Inventory List -->
            <div class="inventory-list">
                <h2>Current Inventory</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock Quantity</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($inventory): ?>
                            <?php foreach ($inventory as $item): ?>
                                <tr>
                                    <td>#INV<?= htmlspecialchars($item['id']) ?></td>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($item['category_name']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>$<?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <a href="edit-inventory.php?id=<?= $item['id'] ?>" class="edit-btn">Edit</a>
                                        <a href="delete-inventory.php?id=<?= $item['id'] ?>" onclick="return confirm('Are you sure?');" class="delete-btn">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No inventory items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add New Inventory Item Form -->
            <div class="add-inventory-form">
                <h2>Add New Inventory Item</h2>
                <form action="inventory.php" method="post">
                    <div class="form-group">
                        <label for="product-name">Product Name</label>
                        <select id="product-name" name="product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Stock Quantity</label>
                        <input type="number" id="quantity" name="quantity" placeholder="Enter stock quantity" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" id="price" name="price" placeholder="Enter price" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Add Item</button>
                </form>
            </div>

        </section>
    </main>
</body>
</html>
