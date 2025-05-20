<?php
session_start();
include 'config.php'; // Database connection

if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    // Update payment status to 'Confirmed'
    $sql = "UPDATE payments SET status = 'Confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Payment #PAY$payment_id confirmed successfully.";
    } else {
        $_SESSION['message'] = "Error: Unable to confirm payment.";
    }

    $stmt->close();
    header("Location: payments.php");
    exit();
} else {
    header("Location: payments.php");
    exit();
}
?>
