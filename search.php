<?php
require_once __DIR__ . '/config.php';

$q = trim($_GET['q'] ?? '');
$texts = [];
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $pdo->prepare('SELECT t.id, t.title, t.content, t.created_at, u.username FROM texts t JOIN users u ON u.id = t.user_id WHERE t.title LIKE ? OR t.content LIKE ? ORDER BY t.created_at DESC');
    $stmt->execute([$like, $like]);
    $texts = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script defer src="assets/js/script.js"></script>
</head>
<body>
  <nav class="nav">
    <div class="nav-left"><a href="index.php" class="brand">Text Board</a></div>
    <div class="nav-right">
      <form class="search" action="search.php" method="get">
        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search..." required>
        <button type="submit" class="btn">Search</button>
      </form>
      <?php if (is_logged_in()): ?>
        <a class="btn" href="upload.php">New Post</a>
        <a class="btn" href="profile.php">My Posts</a>
        <a class="btn btn-secondary" href="logout.php">Logout (<?php echo htmlspecialchars(current_username()); ?>)</a>
      <?php else: ?>
        <a class="btn" href="login.php">Login</a>
        <a class="btn btn-secondary" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <main class="container">
    <?php include __DIR__ . '/partials/messages.php'; ?>
    <h1>Search Results</h1>
    <?php if ($q === ''): ?>
      <p>Enter a search term.</p>
    <?php else: ?>
      <p>Showing results for "<?php echo htmlspecialchars($q); ?>"</p>
      <div class="cards">
        <?php foreach ($texts as $t): ?>
          <article class="text-card">
            <header class="text-card__header">
              <h2><?php echo htmlspecialchars($t['title']); ?></h2>
              <div class="meta">by <?php echo htmlspecialchars($t['username']); ?> · <?php echo htmlspecialchars($t['created_at']); ?></div>
            </header>
            <div class="text-card__content" data-full="<?php echo htmlspecialchars($t['content']); ?>"></div>
          </article>
        <?php endforeach; ?>
        <?php if (empty($texts)): ?>
          <p>No results found.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>


