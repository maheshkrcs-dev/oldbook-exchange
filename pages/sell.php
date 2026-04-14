<?php
include '../includes/header.php';
include '../includes/db.php';

// 🔒 Only logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ================= FORM SUBMIT =================
if (isset($_POST['submit'])) {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = $_POST['price'];
    $desc = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($price)) {
        $error = "Title and Price are required";
    } else {

        // IMAGE UPLOAD
        $image_name = "default.png";

        if (!empty($_FILES['image']['name'])) {

            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Allowed types
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed)) {

                // Unique filename
                $image_name = time() . "_" . rand(1000,9999) . "." . $ext;
                $upload_path = "../uploads/" . $image_name;

                move_uploaded_file($file['tmp_name'], $upload_path);

            } else {
                $error = "Invalid image format";
            }
        }

        // Insert into DB
        if (!isset($error)) {

            $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, price, description, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issdss", $user_id, $title, $author, $price, $desc, $image_name);

            if ($stmt->execute()) {
                $success = "Book added successfully!";
            } else {
                $error = "Failed to add book";
            }
        }
    }
}
?>

<div class="container" style="max-width:600px;">
    <h3 class="mb-3">Sell Your Book</h3>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="title" class="form-control mb-2" placeholder="Book Title" required>

        <input type="text" name="author" class="form-control mb-2" placeholder="Author">

        <input type="number" name="price" class="form-control mb-2" placeholder="Price" required>

        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

        <input type="file" name="image" class="form-control mb-3">

        <button name="submit" class="btn btn-success w-100">Add Book</button>

    </form>
</div>

<?php include '../includes/footer.php'; ?>