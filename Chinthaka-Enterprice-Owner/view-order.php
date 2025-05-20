<?php
session_start();
include 'config.php'; // Ensure config.php has a valid database connection

// Fetch order details
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    echo "Invalid Order ID";
    exit;
}

$query = "SELECT * FROM orders WHERE id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Order Details</h2>
        <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <p><strong>Order Status:</strong> <span class="status <?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></p>
        <p><strong>Total Amount:</strong> LKR <?php echo number_format($order['total_amount'], 2); ?></p>
        <p><strong>Items List:</strong> <?php echo htmlspecialchars($order['items_list']); ?></p>
        <p><strong>Quantities:</strong> <?php echo htmlspecialchars($order['quantities']); ?></p>
        <p><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes'] ?: 'No additional notes'); ?></p>
        <p><strong>Order Placed:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
        <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($order['updated_at']); ?></p>
        
        <a href="orders.php" class="btn">Back to Orders</a>
    </div>
</body>
</html>

<style>
/* Add CSS for a styled view */
.container {
    max-width: 700px;
    margin: auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

p {
    font-size: 16px;
    margin: 8px 0;
    color: #555;
}

p strong {
    color: #333;
}

.status {
    padding: 5px 10px;
    border-radius: 4px;
    color: #fff;
    font-weight: bold;
}

.status.pending { background-color: #ff9800; }
.status.confirmed { background-color: #4caf50; }
.status.shipped { background-color: #2196f3; }
.status.completed { background-color: #8bc34a; }

.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #6a0dad;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

.btn:hover {
    background-color: #5b0ca3;
}
</style>

<?php
$pdo = null; // Close the database connection if using PDO
?>
