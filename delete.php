<?php
require_once __DIR__ . '/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT id, user_id FROM texts WHERE id = ?');
$stmt->execute([$id]);
$text = $stmt->fetch();
if (!$text) {
    set_flash('error', 'Post not found.');
    header('Location: index.php');
    exit;
}
if ((int)$text['user_id'] !== (int)current_user_id()) {
    set_flash('error', 'You are not allowed to delete this post.');
    header('Location: index.php');
    exit;
}

$del = $pdo->prepare('DELETE FROM texts WHERE id = ?');
$del->execute([$id]);
set_flash('success', 'Post deleted.');
header('Location: profile.php');
exit;
?>


