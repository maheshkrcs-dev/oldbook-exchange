<?php include '../includes/header.php'; ?>

<?php
if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = $_GET['token'];
?>

<div class="container" style="max-width:400px;">
    <h3>Reset Password</h3>

    <form method="POST" action="../includes/reset_password.php">

        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <input type="password" name="password" class="form-control mb-3" placeholder="New Password" required>

        <button name="reset" class="btn btn-success w-100">Reset Password</button>

    </form>
</div>

<?php include '../includes/footer.php'; ?>