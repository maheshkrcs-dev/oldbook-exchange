<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* FETCH USER */
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* HANDLE FORM */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required!";
    } else {

        $image_name = $user['profile_image'] ?? null;

        /* =========================
           OPTION 1: FILE UPLOAD
        ========================== */
        if (!empty($_FILES['image']['name'])) {

            $file = $_FILES['image'];

            $allowed = ['image/jpeg','image/png','image/jpg'];
            if (!in_array($file['type'], $allowed)) {
                $error = "Only JPG/PNG allowed!";
            } elseif ($file['size'] > 2*1024*1024) {
                $error = "Max size 2MB!";
            } else {

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_name = 'user_' . time() . '.' . $ext;

                $upload_path = '../uploads/' . $new_name;

                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $image_name = $new_name;
                } else {
                    $error = "Upload failed!";
                }
            }
        }

        /* =========================
           OPTION 2: GOOGLE DRIVE URL
        ========================== */
        elseif (!empty($image_url)) {

            // Convert Google Drive link
            if (strpos($image_url, 'drive.google.com') !== false) {

                preg_match('/\/d\/(.*?)\//', $image_url, $matches);

                if (!empty($matches[1])) {
                    $file_id = $matches[1];
                    $image_url = "https://drive.google.com/uc?id=" . $file_id;
                }
            }

            $image_data = @file_get_contents($image_url);

            if ($image_data === false) {
                $error = "Invalid or inaccessible image URL!";
            } else {

                $new_name = 'user_' . time() . '.jpg';
                $upload_path = '../uploads/' . $new_name;

                file_put_contents($upload_path, $image_data);
                $image_name = $new_name;
            }
        }

        /* =========================
           UPDATE USER
        ========================== */
        if (!isset($error)) {

            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, profile_image=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $address, $image_name, $user_id);
            $stmt->execute();

            $_SESSION['user_name'] = $name;
            $_SESSION['profile_image'] = $image_name;

            header("Location: profile.php?success=1");
            exit;
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">

<h3 class="mb-4">👤 My Profile</h3>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">Profile updated successfully!</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">

<!-- PROFILE IMAGE -->
<div class="text-center mb-4">
<?php $img = !empty($user['profile_image']) ? $user['profile_image'] : 'default.png'; ?>
<img src="../uploads/<?= htmlspecialchars($img) ?>"
     class="rounded-circle shadow"
     style="width:110px;height:110px;object-fit:cover;">
</div>

<div class="row">

<div class="col-md-6 mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control"
value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control"
value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control"
value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
</div>

<div class="col-md-6 mb-3">
<label>Upload Image</label>
<input type="file" name="image" class="form-control">
</div>

<div class="col-12 mb-3">
<label>Or Paste Google Drive Image URL</label>
<input type="text" name="image_url" class="form-control"
placeholder="https://drive.google.com/file/d/...">
</div>

<div class="col-12 mb-3">
<label>Address</label>
<textarea name="address" class="form-control"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
</div>

</div>

<button class="btn btn-primary w-100">Update Profile</button>

</form>
</div>

<?php include '../includes/footer.php'; ?>