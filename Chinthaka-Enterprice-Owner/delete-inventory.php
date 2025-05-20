<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "Inventory ID is missing.";
    exit;
}

$inventory_id = $_GET['id'];

// Delete inventory item
$query = "DELETE FROM inventory WHERE id = :inventory_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':inventory_id', $inventory_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Inventory item deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete inventory item.";
}

header("Location: inventory.php");
exit;
?>
