<?php
require_once __DIR__ . '/config.php';
if (!is_logged_in()) {
    header('Location: login.php?msg=login_required');
    exit;
}
?>


