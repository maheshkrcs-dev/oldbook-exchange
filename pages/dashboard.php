<?php
include '../includes/header.php';
include '../includes/db.php';

// 🔒 Only logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// DELETE BOOK
if (isset($_GET['delete'])) {
    $book_id = (int) $_GET['delete'];

    // Get image name first
    $stmt = $conn->prepare("SELECT image FROM books WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $image = $row['image'];

        // Delete image file
        if ($image && $image !== 'default.png') {
            $file = "../uploads/" . $image;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    // Delete from DB
    $stmt = $conn->prepare("DELETE FROM books WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $book_id, $user_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

// FETCH USER BOOKS
$stmt = $conn->prepare("SELECT * FROM books WHERE user_id=? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h3 class="mb-4">My Books</h3>

    <div class="row">

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>

        <div class="col-md-3 mb-4">
            <div class="card h-100">

                <img src="/oldbook/uploads/<?php echo htmlspecialchars($row['image'] ?: 'default.png'); ?>" 
                     class="book-img">

                <div class="card-body d-flex flex-column">
                    <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="price">₹<?php echo $row['price']; ?></p>

                    <div class="mt-auto">

                      <!-- EDIT -->
                      <a href="edit_book.php?id=<?php echo $row['id']; ?>" 
                        class="btn btn-warning btn-sm w-100 mb-2">
                        Edit
                      </a>

                     <!-- DELETE -->
                     <a href="?delete=<?php echo $row['id']; ?>" 
                     class="btn btn-danger btn-sm w-100"
                     onclick="return confirm('Delete this book?')">
                     Delete
                     </a>

                    </div>
                </div>

            </div>
        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No books added yet.</p>
    <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>