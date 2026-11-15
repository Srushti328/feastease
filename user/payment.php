<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>alert('Unauthorized access'); window.location.href='track.php';</script>";
    exit;
}

$order_id = (int)$_GET['order_id'];

// Check order belongs to user and is pending
$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id AND user_id = {$_SESSION['user_id']}")->fetch_assoc();

if (!$order || $order['status'] !== 'Payment Pending') {
    echo "<script>alert('Invalid order or already paid'); window.location.href='track.php';</script>";
    exit;
}

// Simulate payment success
$conn->query("UPDATE orders SET status = 'Confirmed' WHERE order_id = $order_id");

echo "<script>alert('Payment successful! Your order is now confirmed.'); window.location.href='track.php';</script>";
exit;
?>