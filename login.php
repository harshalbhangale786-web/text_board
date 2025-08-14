<?php
require_once __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');

    if ($captcha === '' || !isset($_SESSION['captcha_text']) || strcasecmp($captcha, $_SESSION['captcha_text']) !== 0) {
        $errors[] = 'Invalid captcha.';
    }

    if ($identifier === '' || $password === '') {
        $errors[] = 'All fields are required.';
    }

    if (!$errors) {
        $byEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false;
        if ($byEmail) {
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE email = ?');
            $stmt->execute([$identifier]);
        } else {
            $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$identifier]);
        }
        $user = $stmt->fetch();
        if (!$user || $password !== $user['password']) {
            $errors[] = 'Invalid credentials.';
        } else {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            set_flash('success', 'Logged in successfully.');
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script defer src="assets/js/script.js"></script>
  <script>window.addEventListener('DOMContentLoaded',()=>{window.refreshCaptcha && window.refreshCaptcha();});</script>
  <style>.form{max-width:420px;margin:2rem auto;}</style>
</head>
<body>
  <nav class="nav">
    <div class="nav-left"><a href="index.php" class="brand">Text Board</a></div>
    <div class="nav-right">
      <a class="btn" href="register.php">Register</a>
    </div>
  </nav>

  <main class="container">
    <?php include __DIR__ . '/partials/messages.php'; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>
    <h1>Login</h1>
    <form class="form" method="post" action="">
      <div class="form-group">
        <label>Username or Email</label>
        <input type="text" name="identifier" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Captcha</label>
        <div class="captcha-row">
          <img id="captchaImg" src="captcha.php" alt="captcha">
          <button type="button" class="btn btn-secondary" onclick="refreshCaptcha()">Refresh</button>
        </div>
        <input type="text" name="captcha" placeholder="Enter the text above" required>
      </div>
      <button class="btn" type="submit">Login</button>
    </form>
  </main>
</body>
</html>


