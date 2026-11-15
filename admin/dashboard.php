<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access denied! Please login as admin.'); window.location.href='login.php';</script>";
    exit;
}

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $conn->query("UPDATE orders SET status='$new_status' WHERE order_id=$order_id");
}

$result = $conn->query("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC");
?><!DOCTYPE html><html>
<head>
    <title>Admin Dashboard - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <a href="manage_menu.php" class="btn btn-outline-dark mb-3 float-end">Manage Menu</a>
</head>
<body>
<?php include '../includes/header.php'; ?><div class="container mt-4">
    <h2 class="mb-3 text-center">Admin Dashboard - Manage Orders</h2>
    <div class="text-end mb-3">
        <strong>Logged in as:</strong> <?= $_SESSION['admin_user'] ?>
    </div><?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Type</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= $order['event_date'] ?></td>
                        <td><?= $order['event_time'] ?></td>
                        <td><?= $order['guest_count'] ?></td>
                        <td><?= $order['food_type'] ?></td>
                        <td>₹<?= number_format($order['total_price'], 2) ?></td>
                        <td><span class="badge bg-secondary"><?= $order['status'] ?></span></td>
                        <td>
                            <form method="POST" class="d-flex gap-2 flex-wrap">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <option <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                    <option <?= $order['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option <?= $order['status'] == 'Payment Pending' ? 'selected' : '' ?>>Payment Pending</option>
                                    <option <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option <?= $order['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-warning">No orders found.</div>
<?php endif; ?>

</div><?php include '../includes/footer.php'; ?></body>
</html>

