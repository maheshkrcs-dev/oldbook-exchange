<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_GET['id'])) {
    die("Book not found");
}

$book_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT books.*, users.name 
                        FROM books 
                        JOIN users ON books.user_id = users.id 
                        WHERE books.id=?");

$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Book not found");
}

$book = $result->fetch_assoc();
?>

<div class="container">

    <div class="row">

        <div class="col-md-5">
            <img src="<?= $base_url ?>uploads/<?= htmlspecialchars($book['image'] ?: 'default.png'); ?>" 
                 class="img-fluid rounded">
        </div>

        <div class="col-md-7">

            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p class="text-muted">Author: <?= htmlspecialchars($book['author']) ?></p>

            <h4 class="text-success">₹<?= $book['price'] ?></h4>

            <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>

            <p><strong>Seller:</strong> <?= htmlspecialchars($book['name']) ?></p>

            <div class="mt-3">

                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $book['user_id']): ?>

                    <a href="<?= $base_url ?>includes/cart.php?add=<?= $book['id']; ?>" 
                       class="btn btn-success mb-2">🛒 Add to Cart</a>

                    <a href="<?= $base_url ?>includes/wishlist.php?add=<?= $book['id']; ?>" 
                       class="btn btn-outline-danger mb-2">❤️ Wishlist</a>

                    <a href="<?= $base_url ?>includes/chat.php?start=<?= $book['user_id']; ?>" 
                       class="btn btn-secondary mb-2">💬 Chat Seller</a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>