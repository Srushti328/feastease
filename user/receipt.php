<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>alert('Unauthorized'); window.location.href='track.php';</script>";
    exit;
}

$order_id = (int)$_GET['order_id'];
$order = $conn->query("SELECT * FROM orders WHERE order_id = $order_id AND user_id = {$_SESSION['user_id']}")->fetch_assoc();

if (!$order) {
    echo "<script>alert('Order not found'); window.location.href='track.php';</script>";
    exit;
}

$items = $conn->query("SELECT m.dish_name, m.price, oi.quantity FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id WHERE oi.order_id = $order_id");
?><!DOCTYPE html><html>
<head>
    <title>Receipt - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .receipt-box { max-width: 800px; margin: auto; border: 1px solid #ccc; padding: 20px; background: #fff; }
        .receipt-title { font-size: 28px; font-weight: bold; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body class="bg-light">
<div class="receipt-box mt-5">
    <div class="receipt-title">FeastEase - Payment Receipt</div><p><strong>Order ID:</strong> #<?= $order['order_id'] ?> <br>
   <strong>Name:</strong> <?= $_SESSION['user_name'] ?><br>
   <strong>Date:</strong> <?= $order['event_date'] ?> <?= $order['event_time'] ?><br>
   <strong>Guests:</strong> <?= $order['guest_count'] ?><br>
   <strong>Address:</strong> <?= $order['address'] ?><br>
   <strong>Status:</strong> <?= $order['status'] ?><br>
</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Dish</th>
            <th>Price</th>
            <th>Qty</th>
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
        <tr class="fw-bold">
            <td colspan="3" class="text-end">Total</td>
            <td>₹<?= number_format($total, 2) ?></td>
        </tr>
    </tbody>
</table>

<div class="text-center mt-4">
    <a href="track.php" class="btn btn-secondary">Back to Orders</a>
    <button onclick="window.print()" class="btn btn-success">Print Receipt</button>
</div>

</div>
</body>
</html>