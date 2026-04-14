<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* STATS */
$users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$books = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];

$revenue = $conn->query("
    SELECT SUM(total) as total 
    FROM orders WHERE payment_status='paid'
")->fetch_assoc()['total'] ?? 0;

/* DATA */
$pending_books = $conn->query("
    SELECT b.*, u.name FROM books b 
    JOIN users u ON b.user_id=u.id
    WHERE b.status='pending'
");

$all_users = $conn->query("SELECT * FROM users");

$all_books = $conn->query("
    SELECT b.*, u.name FROM books b 
    JOIN users u ON b.user_id=u.id
");
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">

<h2 class="fw-bold mb-4">⚡ Admin Dashboard</h2>

<!-- STATS -->
<div class="row g-3 mb-4">

<div class="col-md-3">
<div class="card dashboard-card bg-primary text-white">
<h6>Users</h6>
<h3><?= $users ?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card dashboard-card bg-success text-white">
<h6>Books</h6>
<h3><?= $books ?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card dashboard-card bg-warning text-dark">
<h6>Orders</h6>
<h3><?= $orders ?></h3>
</div>
</div>

<div class="col-md-3">
<div class="card dashboard-card bg-dark text-white">
<h6>Revenue</h6>
<h3>₹<?= number_format($revenue,2) ?></h3>
</div>
</div>

</div>

<!-- PENDING BOOKS -->
<div class="card p-3 mb-4 shadow-sm">
<h5>📚 Pending Book Approvals</h5>

<table class="table table-hover mt-3">
<thead>
<tr><th>Book</th><th>User</th><th>Action</th></tr>
</thead>

<tbody>
<?php while($b = $pending_books->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($b['title']) ?></td>
<td><?= htmlspecialchars($b['name']) ?></td>
<td>
<a href="../includes/book_action.php?id=<?= $b['id'] ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
<a href="../includes/book_action.php?id=<?= $b['id'] ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- USERS -->
<div class="card p-3 mb-4 shadow-sm">
<h5>👥 Users</h5>

<table class="table table-hover mt-3">
<thead>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
</thead>

<tbody>
<?php while($u = $all_users->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<a href="../includes/delete_user.php?id=<?= $u['id'] ?>" 
class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- BOOKS -->
<div class="card p-3 mb-4 shadow-sm">
<h5>📦 All Books</h5>

<table class="table table-hover mt-3">
<thead>
<tr><th>Title</th><th>User</th><th>Status</th><th>Action</th></tr>
</thead>

<tbody>
<?php while($b = $all_books->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($b['title']) ?></td>
<td><?= htmlspecialchars($b['name']) ?></td>

<td>
<span class="badge 
<?= $b['status']=='approved' ? 'bg-success' : ($b['status']=='rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
<?= $b['status'] ?>
</span>
</td>

<td>
<a href="../includes/delete_book.php?id=<?= $b['id'] ?>" 
class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<?php include '../includes/footer.php'; ?>