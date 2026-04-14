<?php
// =============================
// COMMON FUNCTIONS (SECURE)
// =============================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/* =============================
   SANITIZE OUTPUT (XSS SAFE)
============================= */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/* =============================
   CHECK LOGIN
============================= */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/* =============================
   REQUIRE LOGIN
============================= */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../pages/login.php");
        exit();
    }
}

/* =============================
   GET CURRENT USER ID
============================= */
function userId() {
    return $_SESSION['user_id'] ?? null;
}

/* =============================
   CSRF TOKEN
============================= */
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/* =============================
   SAFE REDIRECT
============================= */
function redirect($url) {
    header("Location: $url");
    exit();
}

/* =============================
   FETCH SINGLE ROW
============================= */
function fetchOne($conn, $query, $types = "", $params = []) {
    $stmt = $conn->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/* =============================
   FETCH MULTIPLE ROWS
============================= */
function fetchAll($conn, $query, $types = "", $params = []) {
    $stmt = $conn->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

/* =============================
   EXECUTE QUERY
============================= */
function executeQuery($conn, $query, $types = "", $params = []) {
    $stmt = $conn->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    return $stmt->execute();
}

/* =============================
   COUNT HELPERS (BADGES)
============================= */
function getCartCount($conn, $uid) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function getWishlistCount($conn, $uid) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function getInboxCount($conn, $uid) {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM messages m
        JOIN conversations c ON m.conversation_id = c.id
        WHERE (c.user1_id = ? OR c.user2_id = ?)
        AND m.sender_id != ?
        AND m.is_read = 0
    ");
    $stmt->bind_param("iii", $uid, $uid, $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

/* =============================
   FORMAT PRICE
============================= */
function price($amount) {
    return "₹" . number_format($amount, 2);
}

/* =============================
   ORDER STATUS BADGE
============================= */
function orderStatusBadge($status) {
    switch($status) {
        case 'completed': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-warning text-dark';
    }
}

/* =============================
   PAYMENT STATUS BADGE
============================= */
function paymentStatusBadge($status) {
    switch($status) {
        case 'paid': return 'bg-success';
        case 'failed': return 'bg-danger';
        default: return 'bg-warning text-dark';
    }
}

/* =============================
   FILE UPLOAD HELPER
============================= */
function uploadImage($file, $folder = "../uploads/") {

    if (empty($file['name'])) {
        return "default.png";
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) {
        return false;
    }

    $name = time() . "_" . rand(1000,9999) . "." . $ext;
    $path = $folder . $name;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $name;
    }

    return false;
}

/* =============================
   DEBUG (ONLY DEV)
============================= */
function dd($data) {
    echo "<pre>";
    print_r($data);
    die;
}