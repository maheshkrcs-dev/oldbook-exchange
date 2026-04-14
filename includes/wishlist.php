<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ADD
if (isset($_GET['add'])) {
    $book_id = (int) $_GET['add'];

    $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    header("Location: ../index.php");
    exit();
}

// REMOVE
if (isset($_GET['remove'])) {
    $book_id = (int) $_GET['remove'];

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    header("Location: ../pages/wishlist.php");
    exit();
}