<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>alert('Unauthorized access'); window.location.href='track.php';</script>";
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Validate the order belongs to the user
$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id AND user_id = $user_id")->fetch_assoc();
if (!$order) {
    echo "<script>alert('Invalid order'); window.location.href='track.php';</script>";
    exit;
}

// Fetch order items
$items = $conn->query("
    SELECT m.dish_name, m.price, oi.quantity 
    FROM order_items oi 
    JOIN menu m ON oi.menu_id = m.menu_id 
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container mt-5">
    <h3>Order #<?= $order_id ?> Details</h3>
    <p><strong>Event:</strong> <?= $order['event_date'] ?> | <?= $order['event_time'] ?> | <?= $order['food_type'] ?> | <?= $order['guest_count'] ?> guests</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Dish</th>
            <th>Price (₹)</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; while ($item = $items->fetch_assoc()): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= $item['dish_name'] ?></td>
            <td>₹<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₹<?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="3" class="text-end fw-bold">Total</td>
            <td class="fw-bold">₹<?= number_format($total, 2) ?></td>
        </tr>
    </tbody>
</table>

<a href="track.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>



