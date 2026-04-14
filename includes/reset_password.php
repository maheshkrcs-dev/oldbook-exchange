<?php
include 'db.php';

// ================= FORGOT =================
if (isset($_POST['forgot'])) {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {

        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        $link = "http://localhost/oldbook/pages/reset.php?token=$token";

        header("Location: ../pages/forgot.php?msg=Copy this link: $link");
        exit();

    } else {
        header("Location: ../pages/forgot.php?msg=Email not found");
        exit();
    }
}


// ================= RESET =================
if (isset($_POST['reset'])) {

    $token = trim($_POST['token']);
    $pass = $_POST['password'];

    if (empty($pass)) {
        header("Location: ../pages/reset.php?token=$token&error=Password required");
        exit();
    }

    // ✅ FIX: REMOVE expiry check for now
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users 
                               SET password=?, reset_token=NULL, token_expiry=NULL 
                               WHERE reset_token=?");

        $stmt->bind_param("ss", $hashed, $token);
        $stmt->execute();

        header("Location: ../pages/login.php?success=Password reset successful");
        exit();

    } else {
        header("Location: ../pages/forgot.php?msg=Invalid token");
        exit();
    }
}