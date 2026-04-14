<?php include '../includes/header.php'; ?>

<div class="container" style="max-width:400px;">
    <h3 class="mb-3">Login</h3>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= $_GET['error'] ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= $_GET['success'] ?></div>
    <?php endif; ?>

    <form method="POST" action="../includes/auth.php">
        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

        <button name="login" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="mt-3">
        Don't have account? <a href="register.php">Register</a>
    </p>
    <p class="mt-2">
    <a href="forgot.php">Forgot Password?</a>
</p>
</div>

<?php include '../includes/footer.php'; ?>