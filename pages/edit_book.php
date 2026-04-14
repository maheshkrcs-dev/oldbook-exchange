<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = (int) $_GET['id'];

// FETCH BOOK
$stmt = $conn->prepare("SELECT * FROM books WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Book not found");
}

$book = $result->fetch_assoc();

// UPDATE
if (isset($_POST['update'])) {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = $_POST['price'];
    $desc = trim($_POST['description']);

    $image_name = $book['image'];

    // IMAGE UPDATE
    if (!empty($_FILES['image']['name'])) {

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {

            // Delete old image
            if ($image_name && $image_name !== 'default.png') {
                $old = "../uploads/" . $image_name;
                if (file_exists($old)) unlink($old);
            }

            // Upload new
            $image_name = time().rand(1000,9999).".".$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$image_name);
        }
    }

    // UPDATE DB
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, price=?, description=?, image=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssdssii", $title, $author, $price, $desc, $image_name, $book_id, $user_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Update failed";
    }
}
?>

<div class="container" style="max-width:600px;">
    <h3 class="mb-3">Edit Book</h3>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="title" class="form-control mb-2" 
               value="<?= htmlspecialchars($book['title']) ?>" required>

        <input type="text" name="author" class="form-control mb-2" 
               value="<?= htmlspecialchars($book['author']) ?>">

        <input type="number" name="price" class="form-control mb-2" 
               value="<?= $book['price'] ?>" required>

        <textarea name="description" class="form-control mb-2"><?= htmlspecialchars($book['description']) ?></textarea>

        <p>Current Image:</p>
        <img src="/oldbook/uploads/<?= $book['image'] ?>" width="100" class="mb-2">

        <input type="file" name="image" class="form-control mb-3">

        <button name="update" class="btn btn-success w-100">Update Book</button>

    </form>
</div>

<?php include '../includes/footer.php'; ?>