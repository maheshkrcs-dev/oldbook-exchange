<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

$base_url = "http://localhost/oldbook/";

$cart_count = $wishlist_count = $unread_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($cart_count);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($wishlist_count);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE is_read=0 AND sender_id!=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($unread_count);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>OldBook</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">

<style>
.navbar {
    background: #212529;
}

.nav-icon {
    position: relative;
    margin-right: 15px;
}

.badge-notify {
    position: absolute;
    top: -5px;
    right: -8px;
    font-size: 10px;
}
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
<div class="container">

<a class="navbar-brand fw-bold" href="<?= $base_url ?>">📚 OldBook</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="nav">
<ul class="navbar-nav ms-auto align-items-center">

<li class="nav-item"><a class="nav-link" href="<?= $base_url ?>">Home</a></li>
<li class="nav-item"><a class="nav-link" href="<?= $base_url ?>pages/sell.php">Sell</a></li>

<?php if(isset($_SESSION['user_id'])): ?>

<!-- CART -->
<li class="nav-item nav-icon">
<a class="nav-link" href="<?= $base_url ?>pages/cart.php">🛒
<?php if($cart_count > 0): ?>
<span class="badge bg-danger badge-notify"><?= $cart_count ?></span>
<?php endif; ?></a>
</li>

<!-- WISHLIST -->
<li class="nav-item nav-icon">
<a class="nav-link" href="<?= $base_url ?>pages/wishlist.php">❤️
<?php if($wishlist_count > 0): ?>
<span class="badge bg-danger badge-notify"><?= $wishlist_count ?></span>
<?php endif; ?></a>
</li>

<!-- MESSAGES -->
<li class="nav-item nav-icon">
<a class="nav-link" href="<?= $base_url ?>pages/inbox.php">💬
<?php if($unread_count > 0): ?>
<span class="badge bg-danger badge-notify"><?= $unread_count ?></span>
<?php endif; ?></a>
</li>

<!-- USER DROPDOWN -->
<li class="nav-item dropdown">
<a class="nav-link dropdown-toggle d-flex align-items-center"
   href="#" data-bs-toggle="dropdown">

<?= $_SESSION['user_name'] ?>
</a>

<ul class="dropdown-menu dropdown-menu-end shadow">

<li><a class="dropdown-item" href="<?= $base_url ?>pages/profile.php">👤 Profile</a></li>
<li><a class="dropdown-item" href="<?= $base_url ?>pages/orders.php">📦 Orders</a></li>
<li><a class="dropdown-item" href="<?= $base_url ?>pages/dashboard.php">Dashboard</a></li>

<li><hr class="dropdown-divider"></li>

<li>
<a class="dropdown-item text-danger" href="<?= $base_url ?>pages/logout.php">
🚪 Logout
</a>
</li>

</ul>
</li>

<?php else: ?>

<li class="nav-item"><a class="nav-link" href="<?= $base_url ?>pages/login.php">Login</a></li>

<li class="nav-item">
<a class="btn btn-warning ms-2" href="<?= $base_url ?>pages/register.php">
Register
</a>
</li>

<?php endif; ?>

</ul>
</div>
</div>
</nav>

<div class="container mt-4">