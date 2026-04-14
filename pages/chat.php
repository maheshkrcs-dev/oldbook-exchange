<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$convo_id = (int) $_GET['id'];

$sql = "SELECT messages.*, users.name 
        FROM messages 
        JOIN users ON messages.sender_id = users.id
        WHERE conversation_id=?
        ORDER BY messages.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $convo_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h3 class="mb-3">Chat 💬</h3>

    <div class="border p-3 mb-3" style="height:300px; overflow-y:auto;">

        <?php while ($msg = $result->fetch_assoc()): ?>
            <p>
                <strong><?= htmlspecialchars($msg['name']) ?>:</strong>
                <?= htmlspecialchars($msg['message']) ?>
            </p>
        <?php endwhile; ?>

    </div>

    <form method="POST" action="../includes/chat.php">
        <input type="hidden" name="convo_id" value="<?= $convo_id ?>">

        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type message..." required>
            <button name="send" class="btn btn-primary">Send</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>