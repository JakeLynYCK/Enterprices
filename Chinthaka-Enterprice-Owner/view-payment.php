<?php
session_start();
include 'config.php'; // Database connection

// Check if payment ID is set in the URL
if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    // Fetch payment details from the database
    $sql = "SELECT payments.id, users.username AS customer_name, payments.amount, payments.payment_method, payments.status, payments.created_at
            FROM payments
            INNER JOIN users ON payments.customer_id = users.id
            WHERE payments.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
    } else {
        echo "Payment not found.";
        exit();
    }
} else {
    header("Location: payments.php");
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Payment - Chinthaka Enterprises</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
</head>
<body>
    <aside class="sidebar">
        <!-- Sidebar content -->
    </aside>

    <main class="main-content">
        <header>
            <h1>Payment Details</h1>
        </header>

        <section class="content">
            <div class="payment-details">
                <h2>Payment #PAY<?php echo $payment['id']; ?></h2>
                <p><strong>Customer Name:</strong> <?php echo $payment['customer_name']; ?></p>
                <p><strong>Amount:</strong> $<?php echo $payment['amount']; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $payment['payment_method']; ?></p>
                <p><strong>Status:</strong> <?php echo $payment['status']; ?></p>
                <p><strong>Date:</strong> <?php echo $payment['created_at']; ?></p>

                <?php if ($payment['status'] == 'Pending'): ?>
                    <a href="confirm-payment.php?id=<?php echo $payment['id']; ?>" class="confirm-btn">Confirm Payment</a>
                <?php endif; ?>

                <a href="payments.php" class="back-btn">Back to Payments</a>
            </div>
        </section>
    </main>
</body>
</html>

<?php $conn->close(); ?>
