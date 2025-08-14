<?php
require_once __DIR__ . '/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT id, user_id, title, content FROM texts WHERE id = ?');
$stmt->execute([$id]);
$text = $stmt->fetch();
if (!$text) {
    set_flash('error', 'Post not found.');
    header('Location: index.php');
    exit;
}
if ((int)$text['user_id'] !== (int)current_user_id()) {
    set_flash('error', 'You are not allowed to edit this post.');
    header('Location: index.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        $errors[] = 'Title and content are required.';
    } else {
        $upd = $pdo->prepare('UPDATE texts SET title = ?, content = ? WHERE id = ?');
        $upd->execute([$title, $content, $id]);
        set_flash('success', 'Post updated.');
        header('Location: profile.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Post</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <nav class="nav">
    <div class="nav-left"><a href="index.php" class="brand">Text Board</a></div>
    <div class="nav-right">
      <a class="btn" href="profile.php">My Posts</a>
      <a class="btn btn-secondary" href="logout.php">Logout (<?php echo htmlspecialchars(current_username()); ?>)</a>
    </div>
  </nav>

  <main class="container">
    <?php include __DIR__ . '/partials/messages.php'; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>
    <h1>Edit Post</h1>
    <form class="form" method="post" action="">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($text['title']); ?>" required>
      </div>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" rows="10" required><?php echo htmlspecialchars($text['content']); ?></textarea>
      </div>
      <button class="btn" type="submit">Save Changes</button>
    </form>
  </main>
</body>
</html>


