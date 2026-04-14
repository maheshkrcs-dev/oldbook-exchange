<?php
require_once 'db.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM books WHERE id=$id");

header("Location: ../pages/admin_dashboard.php");