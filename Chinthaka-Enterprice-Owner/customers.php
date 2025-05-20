<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Add New Customer
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['customer-name'];
    $email = $_POST['customer-email'];
    $phone = $_POST['customer-phone'];

    // Insert customer data into the database
    $insertQuery = "INSERT INTO customers (name, email, phone, role) VALUES (:name, :email, :phone, 'customer')";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);

    if ($stmt->execute()) {
        $_SESSION['success'] = "New customer added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add new customer.";
    }

    header("Location: customers.php");
    exit;
}

// Fetch all customers
$query = "SELECT id, name, email, phone FROM customers WHERE role = 'customer' ORDER BY id ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                <li><a href="add-item.php"><i class="bi bi-plus-square"></i> Add New Item</a></li>
                <li><a href="categories.php"><i class="bi bi-folder-plus"></i> Categories</a></li>
                <li><a href="orders.php"><i class="bi bi-box-seam"></i> Orders</a></li>
                <li><a href="customers.php" class="active"><i class="bi bi-people"></i> Customers</a></li>
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
            <h1>Customer Management</h1>
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

            <!-- Customer List -->
            <div class="customers-list">
                <h2>Customer List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($customers): ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($customer['id']) ?></td>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                    <td><?= htmlspecialchars($customer['email']) ?></td>
                                    <td><?= htmlspecialchars($customer['phone']) ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="edit-customer.php?id=<?= $customer['id'] ?>" class="edit-btn">Edit</a>
                                            <a href="delete-customer.php?id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?');" class="delete-btn">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No customers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add New Customer Form -->
            <div class="add-customer-form">
                <h2>Add New Customer</h2>
                <form action="customers.php" method="POST">
                    <div class="form-group">
                        <label for="customer-name">Name</label>
                        <input type="text" id="customer-name" name="customer-name" placeholder="Enter customer name" required>
                    </div>

                    <div class="form-group">
                        <label for="customer-email">Email</label>
                        <input type="email" id="customer-email" name="customer-email" placeholder="Enter customer email" required>
                    </div>

                    <div class="form-group">
                        <label for="customer-phone">Phone</label>
                        <input type="tel" id="customer-phone" name="customer-phone" placeholder="Enter customer phone" required>
                    </div>

                    <button type="submit" class="submit-btn">Add Customer</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
