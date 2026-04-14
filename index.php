<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
include 'includes/header.php';
include 'includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!-- HERO -->
<div class="text-center mb-5">
    <h1 class="fw-bold">Buy & Sell Old Books Easily</h1>
    <p class="text-muted">Find affordable books or sell your unused ones</p>

    <form method="GET" action="<?= $base_url ?>" class="mx-auto" style="max-width:500px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control" 
                   placeholder="Search by title or author..." 
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-dark">Search</button>
        </div>
    </form>
</div>

<div class="row">

<?php
$sql = "SELECT books.*, users.name 
        FROM books 
        LEFT JOIN users ON books.user_id = users.id";

if (!empty($search)) {
    $sql .= " WHERE books.title LIKE ? OR books.author LIKE ?";
}

$sql .= " ORDER BY books.id DESC";

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
while ($row = $result->fetch_assoc()):
?>

<div class="col-md-3 mb-4">
    <div class="card h-100 shadow-sm">

        <img src="<?= $base_url ?>uploads/<?= htmlspecialchars($row['image'] ?: 'default.png'); ?>" 
             class="book-img">

        <div class="card-body d-flex flex-column">

            <h5><?= htmlspecialchars($row['title']) ?></h5>
            <p class="text-muted"><?= htmlspecialchars($row['author']) ?></p>
            <p class="price">₹<?= $row['price'] ?></p>

            <small class="text-secondary">
                Seller: <?= htmlspecialchars($row['name']) ?>
            </small>

            <div class="mt-auto">

                <!-- VIEW -->
                <a href="<?= $base_url ?>pages/book.php?id=<?= $row['id']; ?>" 
                   class="btn btn-primary w-100 mb-2">
                   View Details
                </a>

                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $row['user_id']): ?>

                    <a href="<?= $base_url ?>includes/cart.php?add=<?= $row['id']; ?>" 
                       class="btn btn-success btn-sm w-100 mb-2">
                       🛒 Add to Cart
                    </a>

                    <a href="<?= $base_url ?>includes/wishlist.php?add=<?= $row['id']; ?>" 
                       class="btn btn-outline-danger btn-sm w-100 mb-2">
                       ❤️ Wishlist
                    </a>

                    <a href="<?= $base_url ?>includes/chat.php?start=<?= $row['user_id']; ?>" 
                       class="btn btn-secondary btn-sm w-100">
                       💬 Chat Seller
                    </a>

                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<?php endwhile; else: ?>

<div class="text-center">
    <p class="text-muted">No books found</p>
</div>

<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
