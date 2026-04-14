<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die("Access denied");
}

$result = $conn->query("
    SELECT orders.*, users.name 
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
");
?>

<div class="container mt-4">
    <h2>Admin Panel - Orders 🛠️</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Update</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>#<?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>₹<?= $row['total'] ?></td>

            <td><?= ucfirst($row['status']) ?></td>
            <td><?= ucfirst($row['payment_status']) ?></td>

            <td>
                <form method="POST" action="../includes/update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                    <select name="status" class="form-control mb-1">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                    <button class="btn btn-sm btn-primary">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>