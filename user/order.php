<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_menu'])) {
    $_SESSION['order_info'] = [
        'food_type' => $_POST['food_type'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'guest_count' => $_POST['guest_count'],
        'address' => $_POST['address'],
        'categories' => $_POST['categories'] ?? []
    ];

    header("Location: menu.php");
    exit;
}
?><!DOCTYPE html><html>
<head>
    <title>Start Order - FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
<?php include '../includes/header.php'; ?>  
<div class="container mt-5">
    <h2>Plan Your Event</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Select Food Preference</label>
            <select name="food_type" class="form-select" required>
                <option value="Veg">Only Veg</option>
                <option value="Non-Veg">Only Non-Veg</option>
                <option value="Both">Veg & Non-Veg</option>
            </select>
        </div><div class="mb-3">
        <label>Select Categories</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="categories[]" value="Starter" checked>
            <label class="form-check-label">Starter</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="categories[]" value="Main Course" checked>
            <label class="form-check-label">Main Course</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="categories[]" value="Dessert" checked>
            <label class="form-check-label">Dessert</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="categories[]" value="Ice Cream" checked>
            <label class="form-check-label">Ice Cream</label>
        </div>
    </div>

    <div class="mb-3">
        <label>Event Date</label>
        <input type="date" name="event_date" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Event Time</label>
        <input type="time" name="event_time" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Number of Guests</label>
        <input type="number" name="guest_count" class="form-control" min="50" max="2000" required>
    </div>
    <div class="mb-3">
        <label>Event Address</label>
        <textarea name="address" class="form-control" rows="3" required></textarea>
    </div>

    <button type="submit" name="show_menu" class="btn btn-success w-100">Next</button>
</form>

</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>



  

