<?php
session_start();
$conn = new mysqli("localhost", "root", "", "feastease");

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access denied!'); window.location.href='login.php';</script>";
    exit;
}

// Handle Add Dish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dish'])) {
    $name = $_POST['dish_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $type = $_POST['veg_nonveg'];
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_path = 'images/' . basename($_FILES['image']['name']);
    }

    $stmt = $conn->prepare("INSERT INTO menu (dish_name, price, category, veg_nonveg, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $name, $price, $category, $type, $image_path);
    $stmt->execute();
    echo "<script>alert('Dish added successfully.'); window.location.href='manage_menu.php';</script>";
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM menu WHERE menu_id = $id");
    echo "<script>alert('Dish deleted.'); window.location.href='manage_menu.php';</script>";
    exit;
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dish'])) {
    $id = $_POST['menu_id'];
    $name = $_POST['dish_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $type = $_POST['veg_nonveg'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_path = 'images/' . basename($_FILES['image']['name']);
        $conn->query("UPDATE menu SET dish_name='$name', price=$price, category='$category', veg_nonveg='$type', image_path='$image_path' WHERE menu_id=$id");
    } else {
        $conn->query("UPDATE menu SET dish_name='$name', price=$price, category='$category', veg_nonveg='$type' WHERE menu_id=$id");
    }

    echo "<script>alert('Dish updated successfully.'); window.location.href='manage_menu.php';</script>";
    exit;
}

$dishes = $conn->query("SELECT * FROM menu ORDER BY category");
$edit_dish = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_dish = $conn->query("SELECT * FROM menu WHERE menu_id = $id")->fetch_assoc();
}
?><!DOCTYPE html><html>
<head>
    <title>Manage Menu - Admin | FeastEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 mb-5">
    <h2 class="mb-4 text-center">Admin - Manage Menu</h2><!-- Add/Edit Dish Form -->
<form method="POST" enctype="multipart/form-data" class="border p-4 rounded mb-5 bg-light">
    <h4><?= $edit_dish ? 'Edit Dish' : 'Add New Dish' ?></h4>
    <div class="row">
        <?php if ($edit_dish): ?><input type="hidden" name="menu_id" value="<?= $edit_dish['menu_id'] ?>"><?php endif; ?>
        <div class="col-md-4 mb-3">
            <input type="text" name="dish_name" class="form-control" placeholder="Dish Name" value="<?= $edit_dish['dish_name'] ?? '' ?>" required>
        </div>
        <div class="col-md-2 mb-3">
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" value="<?= $edit_dish['price'] ?? '' ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <select name="category" class="form-select" required>
                <?php $cats = ['Starter', 'Main Course', 'Dessert', 'Ice Cream']; foreach ($cats as $cat): ?>
                <option value="<?= $cat ?>" <?= (isset($edit_dish) && $edit_dish['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <select name="veg_nonveg" class="form-select" required>
                <option value="Veg" <?= (isset($edit_dish) && $edit_dish['veg_nonveg'] == 'Veg') ? 'selected' : '' ?>>Veg</option>
                <option value="Non-Veg" <?= (isset($edit_dish) && $edit_dish['veg_nonveg'] == 'Non-Veg') ? 'selected' : '' ?>>Non-Veg</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <input type="file" name="image" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <button type="submit" name="<?= $edit_dish ? 'update_dish' : 'add_dish' ?>" class="btn btn-<?= $edit_dish ? 'warning' : 'success' ?> w-100">
                <?= $edit_dish ? 'Update Dish' : 'Add Dish' ?>
            </button>
        </div>
    </div>
</form>

<!-- Dishes List -->
<h4 class="mb-3">Current Menu</h4>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Dish</th>
                <th>Category</th>
                <th>Type</th>
                <th>Price (₹)</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $dishes->fetch_assoc()): ?>
            <tr>
                <td><?= $row['menu_id'] ?></td>
                <td><?= $row['dish_name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['veg_nonveg'] ?></td>
                <td><?= $row['price'] ?></td>
                <td>
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="../<?= $row['image_path'] ?>" style="width: 80px; height: 60px; object-fit: cover;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <a href="manage_menu.php?edit=<?= $row['menu_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="manage_menu.php?delete=<?= $row['menu_id'] ?>" onclick="return confirm('Delete this dish?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>