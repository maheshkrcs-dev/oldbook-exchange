<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
    die("Invalid upload");
}

/* VALIDATION */
$allowed = ['image/jpeg', 'image/png', 'image/jpg'];

if (!in_array($_FILES['image']['type'], $allowed)) {
    die("Only JPG/PNG allowed");
}

if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
    die("File too large (max 2MB)");
}

/* UPLOAD */
$filename = time() . "_" . basename($_FILES['image']['name']);
$target = "../uploads/" . $filename;

move_uploaded_file($_FILES['image']['tmp_name'], $target);

/* UPDATE DB */
$stmt = $conn->prepare("UPDATE users SET profile_image=? WHERE id=?");
$stmt->bind_param("si", $filename, $user_id);
$stmt->execute();

/* UPDATE SESSION */
$_SESSION['profile_image'] = $filename;

/* REDIRECT BACK */
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;