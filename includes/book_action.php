<?php
require_once 'db.php';

$id = intval($_GET['id']);
$action = $_GET['action'];

if ($action === 'approve') {
    $conn->query("UPDATE books SET status='approved' WHERE id=$id");
}

if ($action === 'reject') {
    $conn->query("UPDATE books SET status='rejected' WHERE id=$id");
}

header("Location: ../pages/admin_dashboard.php");