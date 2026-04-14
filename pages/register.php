<?php include '../includes/header.php'; ?>

<div class="container" style="max-width:400px;">
    <h3 class="mb-3">Register</h3>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= $_GET['error'] ?></div>
    <?php endif; ?>

    <form method="POST" action="../includes/auth.php">
        <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

        <button name="register" class="btn btn-primary w-100">Register</button>
    </form>

    <p class="mt-3">
        Already have account? <a href="login.php">Login</a>
    </p>
</div>

<?php include '../includes/footer.php'; ?>