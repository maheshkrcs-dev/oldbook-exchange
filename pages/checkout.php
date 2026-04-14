<?php
include '../includes/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT cart.*, books.title, books.price 
    FROM cart 
    JOIN books ON cart.book_id = books.id 
    WHERE cart.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];
?>

<div class="container mt-4">
    <h2>Checkout 🧾</h2>

    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-warning">Your cart is empty.</div>
    <?php else: ?>

    <form method="POST" action="../includes/place_order.php">

    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] = bin2hex(random_bytes(32)) ?>">

    <div class="mb-3">
        <label>Shipping Address</label>
        <textarea name="address" class="form-control" required></textarea>
    </div>

    <h4>Order Summary</h4>

    <table class="table">
        <tr>
            <th>Book</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): 
            $row_total = $row['price'] * $row['quantity'];
            $total += $row_total;
        ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td>₹<?= $row['price'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₹<?= $row_total ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="text-end">
        <h4>Total: ₹<?= $total ?></h4>
    </div>

    <div class="mb-3">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control" required>
            <option value="cod">Cash on Delivery</option>
            <option value="online">Online Payment (Demo)</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success mt-3">Place Order</button>

    </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>