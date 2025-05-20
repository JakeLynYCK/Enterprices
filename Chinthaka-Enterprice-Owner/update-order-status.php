<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if order ID and status are provided
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    echo "Order ID or status is missing.";
    exit;
}

$order_id = $_GET['id'];
$new_status = $_GET['status'];

// Validate the new status
$valid_statuses = ['pending', 'confirmed', 'completed', 'canceled'];
if (!in_array($new_status, $valid_statuses)) {
    echo "Invalid order status.";
    exit;
}

try {
    // Update the order status in the database
    $query = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Redirect to orders.php with a success message
        $_SESSION['success'] = "Order status updated successfully.";
    } else {
        // Redirect with an error message if the update fails
        $_SESSION['error'] = "Failed to update order status.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

// Redirect back to orders.php
header("Location: orders.php");
exit;
?>
