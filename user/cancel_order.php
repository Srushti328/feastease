<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>alert('Unauthorized access'); window.location.href='track.php';</script>";
    exit;
}

$order_id = (int)$_GET['order_id'];

// Check if the order is cancelable
$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id AND user_id = {$_SESSION['user_id']}")->fetch_assoc();

if (!$order || !in_array($order['status'], ['Processing', 'Payment Pending'])) {
    echo "<script>alert('This order cannot be cancelled.'); window.location.href='track.php';</script>";
    exit;
}

// Update status
$conn->query("UPDATE orders SET status = 'Cancelled' WHERE order_id = $order_id");

echo "<script>alert('Order cancelled successfully.'); window.location.href='track.php';</script>";
exit;
?>