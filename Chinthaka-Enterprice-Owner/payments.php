<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch payments with optional status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$query = "SELECT payments.id, customers.name AS customer_name, payments.date, payments.status, payments.amount 
          FROM payments 
          JOIN customers ON payments.customer_id = customers.id";
if ($status_filter !== 'all') {
    $query .= " WHERE payments.status = :status";
}
$query .= " ORDER BY payments.date DESC";

$stmt = $pdo->prepare($query);
if ($status_filter !== 'all') {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payments Management - Chinthaka Enterprises</title>
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
            <h1>Payments Management</h1>
        </header>

        <section class="content">
            <!-- Filter Options -->
            <div class="order-filters">
                <a href="payments.php?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">All Payments</a>
                <a href="payments.php?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending Payments</a>
                <a href="payments.php?status=completed" class="filter-btn <?= $status_filter === 'completed' ? 'active' : '' ?>">Completed Payments</a>
                <a href="payments.php?status=failed" class="filter-btn <?= $status_filter === 'failed' ? 'active' : '' ?>">Failed Payments</a>
            </div>

            <!-- Payments Table -->
            <div class="payments-list">
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No payments available.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="payment-row <?= strtolower($payment['status']) ?>">
                                    <td>#<?= htmlspecialchars($payment['id']) ?></td>
                                    <td><?= htmlspecialchars($payment['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($payment['date']) ?></td>
                                    <td><?= htmlspecialchars($payment['status']) ?></td>
                                    <td>LKR <?= number_format($payment['amount'], 2) ?></td>
                                    <td>
                                        <a href="view-payment.php?id=<?= $payment['id'] ?>" class="view-btn">View</a>
                                        <?php if ($payment['status'] === 'pending'): ?>
                                            <a href="update-payment-status.php?id=<?= $payment['id'] ?>&status=completed" class="complete-btn">Complete</a>
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
