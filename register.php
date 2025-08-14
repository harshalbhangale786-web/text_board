<?php
require_once __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');

    if ($captcha === '' || !isset($_SESSION['captcha_text']) || strcasecmp($captcha, $_SESSION['captcha_text']) !== 0) {
        $errors[] = 'Invalid captcha.';
    }
    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already taken.';
        } else {
            $hash = $password;
            $ins = $pdo->prepare('INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())');
            $ins->execute([$username, $email, $hash]);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['username'] = $username;
            set_flash('success', 'Registration successful.');
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
  <title>Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script defer src="assets/js/script.js"></script>
  <script>window.addEventListener('DOMContentLoaded',()=>{window.refreshCaptcha && window.refreshCaptcha();});</script>
  <style>.form{max-width:480px;margin:2rem auto;}</style>
  </head>
<body>
  <nav class="nav">
    <div class="nav-left"><a href="index.php" class="brand">Text Board</a></div>
    <div class="nav-right">
      <a class="btn" href="login.php">Login</a>
    </div>
  </nav>

  <main class="container">
    <?php include __DIR__ . '/partials/messages.php'; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?>
      </div>
    <?php endif; ?>
    <h1>Create Account</h1>
    <form class="form" method="post" action="">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm" required>
      </div>
      <div class="form-group">
        <label>Captcha</label>
        <div class="captcha-row">
          <img id="captchaImg" src="captcha.php" alt="captcha">
          <button type="button" class="btn btn-secondary" onclick="refreshCaptcha()">Refresh</button>
        </div>
        <input type="text" name="captcha" placeholder="Enter the text above" required>
      </div>
      <button class="btn" type="submit">Register</button>
    </form>
  </main>
</body>
</html>


