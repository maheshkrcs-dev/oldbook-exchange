<?php
session_start();
include 'db.php';

// ================= REGISTER =================
if (isset($_POST['register'])) {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if (empty($name) || empty($email) || empty($pass)) {
        header("Location: ../pages/register.php?error=All fields required");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/register.php?error=Invalid email");
        exit();
    }

    // Check existing user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../pages/register.php?error=Email already exists");
        exit();
    }

    // Hash password
    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    // Insert
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed);

    if ($stmt->execute()) {
        header("Location: ../pages/login.php?success=Registered successfully");
        exit();
    } else {
        header("Location: ../pages/register.php?error=Register failed");
        exit();
    }
}


// ================= LOGIN =================
if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if (empty($email) || empty($pass)) {
        header("Location: ../pages/login.php?error=All fields required");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($pass, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../index.php");
            exit();

        } else {
            header("Location: ../pages/login.php?error=Wrong password");
            exit();
        }

    } else {
        header("Location: ../pages/login.php?error=User not found");
        exit();
    }
}


// ================= LOGOUT =================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}