<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Login required");
}

$user_id = $_SESSION['user_id'];

// CREATE OR GET CONVERSATION
if (isset($_GET['start'])) {
    $other_id = (int) $_GET['start'];

    $user1 = min($user_id, $other_id);
    $user2 = max($user_id, $other_id);

    // check existing
    $stmt = $conn->prepare("SELECT id FROM conversations WHERE user1_id=? AND user2_id=?");
    $stmt->bind_param("ii", $user1, $user2);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $convo_id = $row['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user1, $user2);
        $stmt->execute();
        $convo_id = $stmt->insert_id;
    }

    header("Location: ../pages/chat.php?id=" . $convo_id);
    exit();
}

// SEND MESSAGE
if (isset($_POST['send'])) {

    $convo_id = (int) $_POST['convo_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {

        $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $convo_id, $user_id, $message);
        $stmt->execute();
    }

    header("Location: ../pages/chat.php?id=" . $convo_id);
    exit();
}