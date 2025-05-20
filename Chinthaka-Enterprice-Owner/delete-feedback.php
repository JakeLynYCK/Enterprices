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
    echo "Feedback ID is missing.";
    exit;
}

$feedback_id = $_GET['id'];

// Delete feedback
$query = "DELETE FROM feedback WHERE id = :feedback_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':feedback_id', $feedback_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Feedback message deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete feedback message.";
}

header("Location: feedback.php");
exit;
