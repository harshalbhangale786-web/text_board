<?php
require_once __DIR__ . '/auth_check.php';

$stmt = $pdo->prepare('SELECT id, title, content, created_at FROM texts WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([current_user_id()]);
$texts = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Posts</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script defer src="assets/js/script.js"></script>
</head>
<body>
  <nav class="nav">
    <div class="nav-left"><a href="index.php" class="brand">Text Board</a></div>
    <div class="nav-right">
      <a class="btn" href="upload.php">New Post</a>
      <a class="btn btn-secondary" href="logout.php">Logout (<?php echo htmlspecialchars(current_username()); ?>)</a>
    </div>
  </nav>

  <main class="container">
    <?php include __DIR__ . '/partials/messages.php'; ?>
    <h1>My Posts</h1>
    <div class="cards">
      <?php foreach ($texts as $t): ?>
        <article class="text-card">
          <header class="text-card__header">
            <h2><?php echo htmlspecialchars($t['title']); ?></h2>
            <div class="meta">Created <?php echo htmlspecialchars($t['created_at']); ?></div>
          </header>
          <div class="text-card__content" data-full="<?php echo htmlspecialchars($t['content']); ?>"></div>
          <footer class="text-card__footer">
            <a class="btn" href="edit.php?id=<?php echo (int)$t['id']; ?>">Edit</a>
            <a class="btn btn-danger" href="delete.php?id=<?php echo (int)$t['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a>
          </footer>
        </article>
      <?php endforeach; ?>
      <?php if (empty($texts)): ?>
        <p>You have not posted anything yet. <a href="upload.php">Create your first post</a>.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>


