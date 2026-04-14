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

    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND book_id=?");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
    }

    header("Location: ../index.php");
    exit();
}

// REMOVE
if (isset($_GET['remove'])) {
    $book_id = (int) $_GET['remove'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    header("Location: ../pages/cart.php");
    exit();
}