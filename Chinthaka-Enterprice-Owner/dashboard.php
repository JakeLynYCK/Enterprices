<?php
session_start();
include 'config.php';

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch dashboard data
// Total Sales
$totalSalesQuery = $pdo->query("SELECT SUM(total_amount) AS total_sales FROM orders WHERE status = 'completed'");
$totalSales = $totalSalesQuery->fetchColumn() ?: 0;

// Pending Orders Count
$pendingOrdersQuery = $pdo->query("SELECT COUNT(*) AS pending_orders FROM orders WHERE status = 'pending'");
$pendingOrders = $pendingOrdersQuery->fetchColumn() ?: 0;

// Total Customers Count
$totalCustomersQuery = $pdo->query("SELECT COUNT(*) AS total_customers FROM customers WHERE role = 'customer'");
$totalCustomers = $totalCustomersQuery->fetchColumn() ?: 0;

// New Messages Count
$newMessagesQuery = $pdo->query("SELECT COUNT(*) AS new_messages FROM feedback");
$newMessages = $newMessagesQuery->fetchColumn() ?: 0;

// Recent Orders
$recentOrdersQuery = $pdo->query("SELECT id, customer_name, status, total_amount 
                                  FROM orders 
                                  ORDER BY created_at DESC 
                                  LIMIT 5");
$recentOrders = $recentOrdersQuery->fetchAll(PDO::FETCH_ASSOC);



// Notifications
$notificationsQuery = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
$notifications = $notificationsQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Sidebar included from the main admin template -->
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
            <h1>Welcome to the Admin Dashboard</h1>
            <a href="logout.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </header>

        <!-- Dashboard Cards -->
        <section class="dashboard-cards">
            <div class="card">
                <i class="bi bi-graph-up"></i>
                <h3>Total Sales</h3>
                <p>LKR <?= number_format($totalSales, 2) ?></p>
            </div>
            <div class="card">
                <i class="bi bi-box-seam"></i>
                <h3>Pending Orders</h3>
                <p><?= $pendingOrders ?></p>
            </div>
            <div class="card">
                <i class="bi bi-people"></i>
                <h3>Total Customers</h3>
                <p><?= $totalCustomers ?></p>
            </div>
            <div class="card">
                <i class="bi bi-chat-left-text"></i>
                <h3>New Messages</h3>
                <p><?= $newMessages ?></p>
            </div>
        </section>

        <!-- Recent Orders Table -->
        <section class="recent-orders">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><span class="status <?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td>LKR <?= number_format($order['total_amount'], 2) ?></td>
                            <td><a href="view-order.php?id=<?= $order['id'] ?>" class="view-btn">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Notifications Section -->
        <section class="dashboard-notifications">
            <h2>Notifications</h2>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li><i class="bi bi-bell"></i> <?= htmlspecialchars($notification['message']) ?> - <?= date('M j, Y, g:i a', strtotime($notification['created_at'])) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>
</html>
