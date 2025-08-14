<?php
require_once __DIR__ . '/auth_check.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $errors[] = 'Title and content are required.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO texts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([current_user_id(), $title, $content]);
        set_flash('success', 'Post created.');
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>New Post</title>
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
    <h1>New Post</h1>
    <form class="form" method="post" action="">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" required>
      </div>
      <div class="form-group">
        <label>Content</label>
        <textarea name="content" rows="10" required></textarea>
      </div>
      <button class="btn" type="submit">Publish</button>
    </form>
  </main>
</body>
</html>


