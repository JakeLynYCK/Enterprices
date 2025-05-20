<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch notifications from the database
$query = "SELECT * FROM notifications ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Chinthaka Enterprises</title>
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
            <h1>Notifications</h1>
        </header>

        <section class="content">
            <!-- Notifications List -->
            <div class="notifications-list">
                <h2>Recent Notifications</h2>
                <ul>
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $notification): ?>
                            <li class="notification-item">
                                <span class="notification-icon">
                                    <?php
                                    // Determine the icon based on the type
                                    switch ($notification['type']) {
                                        case 'order':
                                            echo '<i class="bi bi-box"></i>';
                                            break;
                                        case 'message':
                                            echo '<i class="bi bi-envelope"></i>';
                                            break;
                                        case 'inventory':
                                            echo '<i class="bi bi-exclamation-circle"></i>';
                                            break;
                                        case 'payment':
                                            echo '<i class="bi bi-cash-stack"></i>';
                                            break;
                                        default:
                                            echo '<i class="bi bi-info-circle"></i>';
                                    }
                                    ?>
                                </span>
                                <span class="notification-text"><?= htmlspecialchars($notification['message']) ?></span>
                                <span class="notification-time"><?= htmlspecialchars($notification['created_at']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No notifications found.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </main>
</body>
</html>
