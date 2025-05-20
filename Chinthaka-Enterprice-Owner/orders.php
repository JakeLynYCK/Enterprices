<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch orders with optional status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$query = "SELECT id, customer_name, created_at, status, total_amount FROM orders";
if ($status_filter !== 'all') {
    $query .= " WHERE status = :status";
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
if ($status_filter !== 'all') {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Management - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <style>
        /* Styles for action buttons */
        .view-btn {
            color: #fff;
            background-color: #6a0dad;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .confirm-btn {
            color: #fff;
            background-color: #28a745;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .complete-btn {
            color: #fff;
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .view-btn:hover, .confirm-btn:hover, .complete-btn:hover {
            opacity: 0.8;
        }
    </style>
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
                <li><a href="orders.php" class="active"><i class="bi bi-box-seam"></i> Orders</a></li>
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
            <h1>Orders Management</h1>
        </header>

        <section class="content">
            <!-- Filter Options -->
            <div class="order-filters">
                <a href="orders.php?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">All Orders</a>
                <a href="orders.php?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending Orders</a>
                <a href="orders.php?status=confirmed" class="filter-btn <?= $status_filter === 'confirmed' ? 'active' : '' ?>">Confirmed Orders</a>
                <a href="orders.php?status=completed" class="filter-btn <?= $status_filter === 'completed' ? 'active' : '' ?>">Completed Orders</a>
                <a href="orders.php?status=canceled" class="filter-btn <?= $status_filter === 'canceled' ? 'active' : '' ?>">Canceled Orders</a>
            </div>

            <!-- Orders Table -->
            <div class="orders-list">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No orders available.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td><span class="status <?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td>LKR <?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <a href="view-order.php?id=<?= $order['id'] ?>" class="view-btn">View</a>
                                        <?php if ($order['status'] === 'Pending'): ?>
                                            <a href="update-order-status.php?id=<?= $order['id'] ?>&status=confirmed" class="confirm-btn">Confirm</a>
                                        <?php elseif ($order['status'] === 'Confirmed'): ?>
                                            <a href="update-order-status.php?id=<?= $order['id'] ?>&status=completed" class="complete-btn">Complete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
