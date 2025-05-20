<?php
session_start();
include 'config.php'; // Database connection

// Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if customer ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No customer ID specified.";
    header("Location: customers.php");
    exit;
}

$customer_id = $_GET['id'];

// Fetch customer data
$query = "SELECT id, name, email, phone FROM customers WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $_SESSION['error'] = "Customer not found.";
    header("Location: customers.php");
    exit;
}

// Handle form submission for updating customer data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['customer-name'];
    $email = $_POST['customer-email'];
    $phone = $_POST['customer-phone'];

    // Update query
    $updateQuery = "UPDATE customers SET name = :name, email = :email, phone = :phone WHERE id = :id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Customer updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update customer.";
    }

    header("Location: customers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body>
    <main class="main-content">
        <header>
            <h1>Edit Customer</h1>
        </header>

        <section class="content">
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error-message"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="edit-customer.php?id=<?= $customer_id ?>" method="POST" class="edit-customer-form">
                <div class="form-group">
                    <label for="customer-name">Name</label>
                    <input type="text" id="customer-name" name="customer-name" value="<?= htmlspecialchars($customer['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="customer-email">Email</label>
                    <input type="email" id="customer-email" name="customer-email" value="<?= htmlspecialchars($customer['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="customer-phone">Phone</label>
                    <input type="tel" id="customer-phone" name="customer-phone" value="<?= htmlspecialchars($customer['phone']) ?>" required>
                </div>

                <button type="submit" class="submit-btn">Update Customer</button>
            </form>
        </section>
    </main>
</body>
</html>
