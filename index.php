<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FeastEase - Online Catering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('images/catering.jpg') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        color: white;
    }
    .hero {
        height: 100vh;
        background-color: rgba(0,0,0,0.5); /* dark overlay */
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 0 20px;
    }
    .btn-lg {
        width: 200px;
    }
</style>
</head>
<body>
    <div class="hero">
        <h1 class="display-4 mb-3">Welcome to <span class="text-warning">FeastEase</span></h1>
        <p class="lead mb-4">Your trusted catering partner for unforgettable events.</p>
        <div class="d-flex gap-3">
            <a href="user/login.php" class="btn btn-success btn-lg">Customer Login</a>
            <a href="admin/login.php" class="btn btn-outline-light btn-lg">Admin Login</a>
        </div>
    </div>
</body>
</html>
