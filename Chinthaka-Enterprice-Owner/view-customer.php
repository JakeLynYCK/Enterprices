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
    echo "Customer ID is missing.";
    exit;
}

$customer_id = $_GET['id'];

// Fetch customer details
$query = "SELECT id, name, email, phone FROM users WHERE id = :customer_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Customer - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body>
    <main class="main-content">
        <header>
            <h1>Customer Details</h1>
        </header>

        <section class="content">
            <p><strong>Customer ID:</strong> #<?= htmlspecialchars($customer['id']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
            <a href="customers.php" class="btn btn-primary">Back to Customers</a>
        </section>
    </main>
</body>
</html>
