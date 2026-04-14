<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.id, u.name 
        FROM conversations c
        JOIN users u ON (u.id = IF(c.user1_id=?, c.user2_id, c.user1_id))
        WHERE c.user1_id=? OR c.user2_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h3 class="mb-4">Inbox 💬</h3>

    <?php if ($result->num_rows > 0): ?>
        <ul class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item">
                <a href="chat.php?id=<?= $row['id'] ?>">
                    Chat with <?= htmlspecialchars($row['name']) ?>
                </a>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No conversations yet</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>