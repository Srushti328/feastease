<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?><!DOCTYPE html><html>
<head>
    <title>Track Orders - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>  
<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Your Orders</h2><?php if ($orders->num_rows > 0): ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Event Date</th>
                <th>Guests</th>
                <th>Food Type</th>
                <th>Total (₹)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $order['order_id'] ?></td>
                    <td><?= $order['event_date'] ?> <?= $order['event_time'] ?></td>
                    <td><?= $order['guest_count'] ?></td>
                    <td><?= $order['food_type'] ?></td>
                    <td><?= $order['total_price'] ?></td>
                    <td>
                        <?php if ($order['status'] === 'Payment Pending'): ?>
                            <a href="payment.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-success">Pay Now</a>
                        <?php else: ?>
                            <span class="badge bg-info text-dark"><?= $order['status'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="view_order.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-primary mb-1">View</a>
                        <?php if ($order['status'] === 'Confirmed'): ?>
                            <a href="receipt.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-dark">Receipt</a>
                        <?php endif; ?>
                        <?php if (in_array($order['status'], ['Processing', 'Payment Pending'])): ?>
                        <a href="cancel_order.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
<?php else: ?>
    <p class="text-center">You have not placed any orders yet.</p>
<?php endif; ?>

</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

