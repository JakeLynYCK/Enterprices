<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if customer ID is provided
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Delete query
    $deleteQuery = "DELETE FROM customers WHERE id = :id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Customer deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete customer.";
    }
} else {
    $_SESSION['error'] = "No customer ID specified.";
}

header("Location: customers.php");
exit;
?>
