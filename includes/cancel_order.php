<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

if (!isset($_POST['order_id']) || !is_numeric($_POST['order_id'])) {
    die("Invalid request");
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['user_id'];

/* VERIFY */
$stmt = $conn->prepare("
    SELECT status FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Unauthorized");
}

if ($order['status'] !== 'pending') {
    die("Cannot cancel");
}

/* UPDATE */
$stmt = $conn->prepare("
    UPDATE orders 
    SET status = 'cancelled' 
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();

header("Location: ../pages/orders.php");
exit();