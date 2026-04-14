<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT books.*, users.name 
        FROM wishlist 
        JOIN books ON wishlist.book_id = books.id
        JOIN users ON books.user_id = users.id
        WHERE wishlist.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h3 class="mb-4">My Wishlist ❤️</h3>

    <div class="row">

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>

        <div class="col-md-3 mb-4">
            <div class="card h-100">

                <img src="/oldbook/uploads/<?php echo $row['image']; ?>" class="book-img">

                <div class="card-body d-flex flex-column">
                    <h5><?php echo $row['title']; ?></h5>
                    <p class="price">₹<?php echo $row['price']; ?></p>

                    <div class="mt-auto">
                        <a href="../includes/wishlist.php?remove=<?php echo $row['id']; ?>" 
                           class="btn btn-danger btn-sm w-100">
                           Remove
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No items in wishlist</p>
    <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>