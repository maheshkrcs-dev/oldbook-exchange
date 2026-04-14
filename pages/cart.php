<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT books.*, cart.quantity 
        FROM cart 
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<div class="container">
    <h3 class="mb-4">My Cart 🛒</h3>

    <table class="table table-bordered">
        <tr>
            <th>Book</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $sub = $row['price'] * $row['quantity'];
                $total += $sub;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td>₹<?= $row['price'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>₹<?= $sub ?></td>
                <td>
                    <a href="../includes/cart.php?remove=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm">Remove</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Cart is empty</td></tr>
        <?php endif; ?>
    </table>

    <div class="d-flex justify-content-end mt-4">
    <div class="card p-3 shadow-sm" style="min-width: 250px;">
        <h5 class="mb-0 d-flex justify-content-between">
            <span>Total:</span>
            <span class="text-success">₹<?= number_format($total, 2) ?></span>
        </h5>
        <div class="d-flex justify-content-end mt-3">
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
    </div>
    
</div>
</div>

<?php include '../includes/footer.php'; ?>