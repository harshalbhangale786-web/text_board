<?php
require_once __DIR__ . '/config.php';
header('Content-Type: text/html; charset=utf-8');

$checks = [];

// PHP version
$checks[] = [
    'label' => 'PHP version (>= 7.4 recommended)',
    'ok' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'detail' => PHP_VERSION,
];

// GD extension for captcha
$checks[] = [
    'label' => 'GD extension loaded',
    'ok' => extension_loaded('gd') && function_exists('imagecreatetruecolor'),
    'detail' => extension_loaded('gd') ? 'gd enabled' : 'gd missing',
];

// Sessions
$_SESSION['__health_check'] = 'ok';
$sessionPath = ini_get('session.save_path');
$checks[] = [
    'label' => 'Session available',
    'ok' => isset($_SESSION['__health_check']) && $_SESSION['__health_check'] === 'ok',
    'detail' => 'save_path=' . ($sessionPath ?: '(default)'),
];

// PDO + DB connectivity
$dbOk = false; $dbMsg = '';
try {
    $stmt = $pdo->query('SELECT 1');
    $dbOk = (bool)$stmt->fetchColumn();
    $dbMsg = 'connected to database';
} catch (Throwable $e) {
    $dbOk = false;
    $dbMsg = 'DB error: ' . $e->getMessage();
}
$checks[] = [
    'label' => 'Database connection',
    'ok' => $dbOk,
    'detail' => $dbMsg,
];

// Users table exists
$usersOk = false; $usersMsg = '';
try {
    $pdo->query('SELECT COUNT(*) FROM users');
    $usersOk = true; $usersMsg = 'users table found';
} catch (Throwable $e) {
    $usersOk = false; $usersMsg = 'users table missing: ' . $e->getMessage();
}
$checks[] = [
    'label' => 'Users table',
    'ok' => $usersOk,
    'detail' => $usersMsg,
];

// Texts table exists
$textsOk = false; $textsMsg = '';
try {
    $pdo->query('SELECT COUNT(*) FROM texts');
    $textsOk = true; $textsMsg = 'texts table found';
} catch (Throwable $e) {
    $textsOk = false; $textsMsg = 'texts table missing: ' . $e->getMessage();
}
$checks[] = [
    'label' => 'Texts table',
    'ok' => $textsOk,
    'detail' => $textsMsg,
];

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Health Check</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .hc{max-width:720px;margin:24px auto;padding:16px;background:#111827;border:1px solid #0b1a34;border-radius:10px}
    .row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed #1f2937}
    .row:last-child{border-bottom:none}
    .ok{color:#10b981}
    .fail{color:#ef4444}
  </style>
  </head>
<body>
  <div class="hc">
    <h2>Text Board - Environment Health</h2>
    <?php foreach ($checks as $c): ?>
      <div class="row">
        <div><?php echo htmlspecialchars($c['label']); ?></div>
        <div><?php echo $c['ok'] ? '<strong class="ok">OK</strong>' : '<strong class="fail">FAIL</strong>'; ?></div>
      </div>
      <div style="color:#94a3b8;font-size:12px;margin:-6px 0 6px 0;">Detail: <?php echo htmlspecialchars($c['detail']); ?></div>
    <?php endforeach; ?>
    <p>Captcha image test (inline):</p>
    <div style="display:flex;align-items:center;gap:8px;margin:8px 0;">
      <img id="hc_captcha" src="captcha.php?ts=<?php echo time(); ?>" alt="captcha" style="border:1px solid #1f2937;border-radius:6px;">
      <button class="btn" onclick="document.getElementById('hc_captcha').src='captcha.php?ts='+(Date.now())">Refresh</button>
      <a class="btn btn-secondary" href="captcha.php" target="_blank">Open in new tab</a>
    </div>
    <p style="color:#94a3b8;font-size:12px;">Remove this file after testing: health.php</p>
  </div>
</body>
</html>


