<?php include '../includes/header.php'; ?>

<div class="container" style="max-width:400px;">
    <h3>Forgot Password</h3>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= $_GET['msg'] ?></div>
    <?php endif; ?>

    <form method="POST" action="../includes/reset_password.php">
        <input type="email" name="email" class="form-control mb-3" placeholder="Enter your email" required>
        <button name="forgot" class="btn btn-dark w-100">Send Reset Link</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>