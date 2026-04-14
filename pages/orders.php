<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>My Orders 📦</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">No orders found.</div>
    <?php else: ?>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>

                    <td class="fw-bold text-success">
                        ₹<?= number_format($row['total'], 2) ?>
                    </td>

                    <!-- ORDER STATUS -->
                    <td>
                        <span class="badge 
                        <?php 
                        switch($row['status']) {
                            case 'completed': echo 'bg-success'; break;
                            case 'cancelled': echo 'bg-danger'; break;
                            default: echo 'bg-warning text-dark';
                        }
                        ?>">
                        <?= ucfirst($row['status']) ?>
                        </span>
                    </td>

                    <!-- PAYMENT STATUS -->
                    <td>
                        <span class="badge 
                        <?php 
                        switch($row['payment_status']) {
                            case 'paid': echo 'bg-success'; break;
                            case 'failed': echo 'bg-danger'; break;
                            default: echo 'bg-warning text-dark';
                        }
                        ?>">
                        <?= ucfirst($row['payment_status']) ?>
                        </span>
                    </td>

                    <td><?= $row['created_at'] ?></td>

                    <td>
                        <a href="order_details.php?id=<?= $row['id'] ?>" 
                           class="btn btn-sm btn-primary">
                           View
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>