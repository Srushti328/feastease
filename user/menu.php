<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_info'])) {
    echo "<script>alert('Access denied. Please start your order.'); window.location.href='order.php';</script>";
    exit;
}

$order_info = $_SESSION['order_info'];
$user_id = $_SESSION['user_id'];
$food_type = $order_info['food_type'];
$event_date = $order_info['event_date'];
$event_time = $order_info['event_time'];
$guest_count = $order_info['guest_count'];
$address = $order_info['address'];
$categories = $order_info['categories'];
$cat_list = "'" . implode("','", array_map('addslashes', $categories)) . "'";

// Build condition based on food type and categories
if ($food_type == "Both") {
    $condition = "category IN ($cat_list)";
} elseif ($food_type == "Veg") {
    $condition = "veg_nonveg = 'Veg' AND category IN ($cat_list)";
} else {
    $condition = "(veg_nonveg = 'Non-Veg' OR category IN ('Dessert', 'Ice Cream')) AND category IN ($cat_list)";
}

// Submit order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    $quantities = $_POST['quantities'];
    $total = 0;
    $valid = false;
    foreach ($quantities as $qty) {
        if ($qty > 0) {
            $valid = true;
            break;
        }
    }

    if (!$valid) {
        echo "<script>alert('Please select at least one dish.'); window.history.back();</script>";
        exit;
    }

    foreach ($quantities as $menu_id => $qty) {
        if ($qty > 0) {
            $menu = $conn->query("SELECT price FROM menu WHERE menu_id = $menu_id")->fetch_assoc();
            $total += $menu['price'] * $qty;
        }
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, event_date, event_time, guest_count, food_type, total_price, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssds", $user_id, $event_date, $event_time, $guest_count, $food_type, $total, $address);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity) VALUES (?, ?, ?)");
    foreach ($quantities as $menu_id => $qty) {
        if ($qty > 0) {
            $stmt2->bind_param("iii", $order_id, $menu_id, $qty);
            $stmt2->execute();
        }
    }

    unset($_SESSION['order_info']);
    echo "<script>alert('Order placed successfully!'); window.location.href='track.php';</script>";
    exit;
}

$dishes = $conn->query("SELECT * FROM menu WHERE $condition");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Dishes - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fdfcfb;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .dish-card {
            transition: transform 0.3s ease;
        }
        .dish-card:hover {
            transform: translateY(-5px);
        }
    </style>
    <script>
        function filterDishes() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const cards = document.querySelectorAll(".dish-card");
            cards.forEach(card => {
                const title = card.querySelector(".card-title").textContent.toLowerCase();
                card.style.display = title.includes(input) ? "block" : "none";
            });
        }
    </script>
</head>
<body class="bg-light">
<?php include '../includes/header.php'; ?>  
<div class="container mt-4">
    <div class="alert alert-info">
        <strong>Event Date:</strong> <?= $event_date ?> |
        <strong>Time:</strong> <?= $event_time ?> |
        <strong>Guests:</strong> <?= $guest_count ?> |
        <strong>Food Type:</strong> <?= $food_type ?> |
        <strong>Address:</strong> <?= $address ?>
    </div>

    <h3 class="mb-3">Select Dishes for Your Menu</h3>
    <div class="search-bar">
        <input type="text" id="searchInput" onkeyup="filterDishes()" class="form-control" placeholder="Search for dishes...">
    </div>

    <form method="POST">
        <div class="row">
            <?php while ($row = $dishes->fetch_assoc()): ?>
                <div class="col-md-4 mb-4 dish-card" data-category="<?= $row['category'] ?>">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($row['image_path'])): ?>
                            <img src="../<?= $row['image_path'] ?>" class="card-img-top" style="height:200px; width:100%; object-fit:cover; border-radius:8px;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['dish_name'] ?></h5>
                            <p class="card-text">
                                <?= $row['category'] ?> |
                                <span class="badge bg-<?= $row['veg_nonveg'] == 'Veg' ? 'success' : 'danger' ?>">
                                    <?= $row['veg_nonveg'] ?>
                                </span>
                            </p>
                            <p><strong>₹<?= $row['price'] ?></strong></p>
                            <input type="number" name="quantities[<?= $row['menu_id'] ?>]" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <button type="submit" name="confirm_order" class="btn btn-primary w-100 fs-5 mt-3">Place Order</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>









