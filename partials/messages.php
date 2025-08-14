<?php
$flashes = get_and_clear_flash();
if (!empty($flashes)) {
    foreach ($flashes as $type => $messages) {
        foreach ($messages as $msg) {
            $class = $type === 'error' ? 'alert-error' : 'alert-success';
            echo '<div class="alert ' . $class . '">' . htmlspecialchars($msg) . '</div>';
        }
    }
}
if (isset($_GET['msg']) && $_GET['msg'] === 'login_required') {
    echo '<div class="alert alert-error">Please login to access that page.</div>';
}
?>


