<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Order ID");
}

$order_id = intval($_GET['id']);
$user_id  = $_SESSION['user_id'];

/* FETCH ORDER */
$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found or unauthorized.");
}

/* FETCH ITEMS */
$stmt = $conn->prepare("
    SELECT oi.*, b.title, b.image 
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

/* CALCULATE TOTAL */
$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $row_total = $row['price'] * $row['quantity'];
    $total += $row_total;
    $items[] = $row;
}
?>

<div class="container mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <h3>Order #<?= $order['id'] ?> 📦</h3>

            <!-- ORDER STATUS -->
            <p>
                <strong>Status:</strong>
                <span class="badge 
                <?php 
                switch($order['status']) {
                    case 'completed': echo 'bg-success'; break;
                    case 'cancelled': echo 'bg-danger'; break;
                    default: echo 'bg-warning text-dark';
                }
                ?>">
                <?= ucfirst($order['status']) ?>
                </span>
            </p>

            <!-- CANCEL BUTTON -->
            <?php if ($order['status'] === 'pending'): ?>
                <form method="POST" action="../includes/cancel_order.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <button class="btn btn-danger btn-sm mb-2"
                        onclick="return confirm('Cancel this order?')">
                        Cancel Order
                    </button>
                </form>
            <?php endif; ?>

            <!-- PAYMENT -->
            <p>
                <strong>Payment:</strong>
                <span class="badge 
                <?php 
                switch($order['payment_status']) {
                    case 'paid': echo 'bg-success'; break;
                    case 'failed': echo 'bg-danger'; break;
                    default: echo 'bg-warning text-dark';
                }
                ?>">
                <?= ucfirst($order['payment_status']) ?>
                </span>
            </p>

            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>

            <p>
                <strong>Total:</strong> 
                <span class="fw-bold text-success">₹<?= number_format($total, 2) ?></span>
            </p>

            <p>
                <strong>Shipping Address:</strong><br>
                <?= nl2br(htmlspecialchars($order['address'])) ?>
            </p>

        </div>
    </div>

    <!-- ITEMS -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>Order Items</h4>

            <?php if (empty($items)): ?>
                <div class="alert alert-warning">No items found.</div>
            <?php else: ?>

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="/oldbook/uploads/<?= htmlspecialchars($item['image']) ?>"
                                 width="60" height="80"
                                 style="object-fit:cover;">
                        </td>

                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td>₹<?= $item['price'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₹<?= $item['price'] * $item['quantity'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php endif; ?>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>