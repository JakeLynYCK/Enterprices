<?php
session_start();
include 'config.php';

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if category ID is provided
if (!isset($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$category_id = $_GET['id'];

// Fetch the current category details
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: categories.php"); // Redirect if category not found
    exit;
}

// Handle form submission for updating category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['category_name'];
    
    // Update the category name in the database
    $update_stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    if ($update_stmt->execute([$new_name, $category_id])) {
        header("Location: categories.php"); // Redirect back to categories list after successful update
        exit;
    } else {
        $error = "Failed to update category.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Sidebar included from the main admin template -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li><a href="add-item.php"><i class="bi bi-plus-square"></i> Add New Item</a></li>
                <li><a href="categories.php" class="active"><i class="bi bi-folder-plus"></i> Categories</a></li>
                <li><a href="orders.php"><i class="bi bi-box-seam"></i> Orders</a></li>
                <li><a href="customers.php"><i class="bi bi-people"></i> Customers</a></li>
                <li><a href="offers.php"><i class="bi bi-gift"></i> Offers</a></li>
                <li><a href="user-profile.php"><i class="bi bi-person"></i> User Profile</a></li>
                <li><a href="company-profile.php"><i class="bi bi-building"></i> Company Profile</a></li>
                <li><a href="reports.php"><i class="bi bi-graph-up"></i> Reports</a></li>
                <li><a href="inventory.php"><i class="bi bi-archive"></i> Inventory</a></li>
                <li><a href="search.php"><i class="bi bi-search"></i> Search</a></li>
                <li><a href="feedback.php"><i class="bi bi-chat-left-text"></i> Feedback</a></li>
                <li><a href="payments.php"><i class="bi bi-credit-card"></i> Payments</a></li>
                <li><a href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1>Edit Category</h1>
        </header>

        <section class="content">
            <div class="edit-category-form">
                <h2>Update Category Name</h2>
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                
                <form method="POST" action="edit_category.php?id=<?= $category_id ?>">
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>
                    <button type="submit" class="submit-btn">Update Category</button>
                    <a href="categories.php" class="cancel-btn">Cancel</a>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
