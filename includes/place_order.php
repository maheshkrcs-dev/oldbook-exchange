<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

$user_id = $_SESSION['user_id'];
$address = trim($_POST['address']);
$payment_method = $_POST['payment_method'] ?? 'cod';

if (empty($address)) {
    header("Location: ../pages/checkout.php");
    exit();
}

/* GET CART */
$stmt = $conn->prepare("
    SELECT cart.*, books.price 
    FROM cart 
    JOIN books ON cart.book_id = books.id 
    WHERE cart.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $row_total = $row['price'] * $row['quantity'];
    $total += $row_total;
    $items[] = $row;
}

if (empty($items)) {
    header("Location: ../pages/cart.php");
    exit();
}

/* PAYMENT */
$payment_status = ($payment_method === 'cod') ? 'pending' : 'paid';

/* INSERT ORDER */
$stmt = $conn->prepare("
    INSERT INTO orders (user_id, total, address, payment_method, payment_status, status) 
    VALUES (?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("idsss", $user_id, $total, $address, $payment_method, $payment_status);
$stmt->execute();

$order_id = $stmt->insert_id;

/* INSERT ITEMS */
foreach ($items as $item) {
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, book_id, price, quantity)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iidi", $order_id, $item['book_id'], $item['price'], $item['quantity']);
    $stmt->execute();
}

/* CLEAR CART */
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

/* REDIRECT */
header("Location: ../pages/order_success.php?id=" . $order_id);
exit();