<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'cart' => 0,
        'wishlist' => 0,
        'messages' => 0
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   CART COUNT
========================= */
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

/* =========================
   WISHLIST COUNT
========================= */
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

/* =========================
   UNREAD MESSAGES
========================= */
$stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM messages 
    WHERE is_read=0 AND sender_id != ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

/* =========================
   RETURN JSON
========================= */
echo json_encode([
    'cart' => $cart,
    'wishlist' => $wishlist,
    'messages' => $messages
]);