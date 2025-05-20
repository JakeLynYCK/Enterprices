<?php
session_start();
include 'config.php'; // Database connection

// Placeholder function to generate sales reports based on date range
function getReportData($type, $startDate = null, $endDate = null) {
    global $pdo;
    
    if ($type === 'monthly') {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS period, SUM(total_amount) AS total_sales FROM orders WHERE status = 'Completed' GROUP BY period";
    } elseif ($type === 'annual') {
        $sql = "SELECT YEAR(created_at) AS period, SUM(total_amount) AS total_sales FROM orders WHERE status = 'Completed' GROUP BY period";
    } elseif ($type === 'custom' && $startDate && $endDate) {
        $sql = "SELECT created_at AS period, SUM(total_amount) AS total_sales FROM orders WHERE created_at BETWEEN :startDate AND :endDate AND status = 'Completed'";
    } else {
        return [];
    }

    $stmt = $pdo->prepare($sql);
    if ($type === 'custom') {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch inventory data
function getInventoryData() {
    global $pdo;
    $sql = "SELECT product_name, quantity, price FROM inventory";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submissions for date filters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['report_type'];
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $reportData = getReportData($type, $startDate, $endDate);
}

// Get inventory data
$inventoryData = getInventoryData();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
</head>
<body>
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

    <main class="main-content">
        <header>
            <h1>Reports</h1>
        </header>

        <section class="content">
            <!-- Date Filter Options -->
            <form action="reports.php" method="post" class="date-filters">
                <h2>Select Report Duration</h2>
                
                <!-- Hidden input to store selected report type -->
                <input type="hidden" name="report_type" id="report_type" value="">

                <!-- Buttons for Monthly, Annual, Custom -->
                <button type="submit" onclick="setReportTypeAndSubmit('monthly')">Monthly</button>
                <button type="submit" onclick="setReportTypeAndSubmit('annual')">Annually</button>
                <button type="button" onclick="showCustomDateRange()">Custom</button>
                
                <!-- Custom Date Range inputs (shown only for "Custom") -->
                <div id="custom-date-range" style="display: none;">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" id="start-date">
                    <label>End Date:</label>
                    <input type="date" name="end_date" id="end-date">
                    <button type="submit" onclick="setReportTypeAndSubmit('custom')">Generate Report</button>
                </div>
            </form>

            <!-- Sales Reports Section -->
            <div class="report-section">
                <h2>Sales Reports</h2>
                <p>Overview of sales performance.</p>
                <div class="report-chart" id="sales-report-chart">
                    <?php if (isset($reportData) && !empty($reportData)): ?>
                        <table>
                            <tr><th>Period</th><th>Total Sales (LKR)</th></tr>
                            <?php foreach ($reportData as $data): ?>
                                <tr><td><?= htmlspecialchars($data['period']) ?></td><td>LKR <?= number_format($data['total_sales'], 2) ?></td></tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No sales data available for the selected period.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Inventory Reports Section -->
            <div class="report-section">
                <h2>Inventory Status</h2>
                <p>Current status of inventory items.</p>
                <div class="report-chart" id="inventory-report-chart">
                    <?php if (!empty($inventoryData)): ?>
                        <table>
                            <tr><th>Product Name</th><th>Quantity</th><th>Price (LKR)</th></tr>
                            <?php foreach ($inventoryData as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td>LKR <?= number_format($item['price'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No inventory data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary Notes Section -->
            <div class="report-section">
                <h2>Summary Notes</h2>
                <textarea class="summary-notes" rows="6" placeholder="Add summary notes here..."></textarea>
            </div>

            <!-- Download as PDF -->
            <button onclick="downloadPDF()" class="btn download-btn"><i class="bi bi-file-earmark-arrow-down"></i> Download Report as PDF</button>
        </section>
    </main>

    <script>
        function setReportTypeAndSubmit(type) {
            document.getElementById('report_type').value = type;
        }

        function showCustomDateRange() {
            document.getElementById('report_type').value = 'custom';
            document.getElementById('custom-date-range').style.display = 'block';
        }

        function downloadPDF() {
            alert('PDF download functionality will be implemented here.');
        }
    </script>
</body>
</html>
