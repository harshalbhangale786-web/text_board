<?php
require_once __DIR__ . '/config.php';

$length = 6;
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$captcha_text = '';
for ($i = 0; $i < $length; $i++) {
    $captcha_text .= $chars[random_int(0, strlen($chars) - 1)];
}
$_SESSION['captcha_text'] = $captcha_text;

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Ensure no output was sent before image/svg
if (function_exists('ob_get_length') && ob_get_length()) {
    ob_clean();
}

$width = 140; $height = 44;

if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
    $im = imagecreatetruecolor($width, $height);
    $bg = imagecolorallocate($im, 240, 240, 240);
    $fg = imagecolorallocate($im, 30, 30, 30);
    $noise1 = imagecolorallocate($im, 180, 180, 180);
    $noise2 = imagecolorallocate($im, 210, 210, 210);

    imagefilledrectangle($im, 0, 0, $width, $height, $bg);
    for ($i = 0; $i < 8; $i++) {
        imageline($im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noise1);
    }
    for ($i = 0; $i < 300; $i++) {
        imagesetpixel($im, rand(0, $width), rand(0, $height), $noise2);
    }

    $font = 5;
    $char_width = imagefontwidth($font);
    $char_height = imagefontheight($font);
    $total_width = $char_width * $length;
    $x = intval(($width - $total_width) / 2);
    $y = intval(($height - $char_height) / 2);
    for ($i = 0; $i < $length; $i++) {
        $offset = rand(-2, 2);
        imagestring($im, $font, $x + $i * $char_width, $y + $offset, $captcha_text[$i], $fg);
    }

    header('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
    exit;
}

// Fallback: SVG captcha (no GD required)
header('Content-Type: image/svg+xml');
$bg = '#f3f4f6';
$fg = '#111827';
$noise1 = '#cbd5e1';
$noise2 = '#e5e7eb';
$chars = str_split($captcha_text);
$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.(int)$width.'" height="'.(int)$height.'">';
$svg .= '<rect width="100%" height="100%" fill="'.$bg.'"/>';
for ($i = 0; $i < 8; $i++) {
    $x1 = rand(0, $width); $y1 = rand(0, $height);
    $x2 = rand(0, $width); $y2 = rand(0, $height);
    $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="'.$noise1.'" stroke-width="1" />';
}
for ($i = 0; $i < 80; $i++) {
    $cx = rand(0, $width); $cy = rand(0, $height);
    $svg .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="0.7" fill="'.$noise2.'" />';
}
$startX = 16; $y = 28;
foreach ($chars as $i => $ch) {
    $x = $startX + $i * 18;
    $rot = rand(-18, 18);
    $svg .= '<g transform="rotate('.$rot.' '.$x.' '.$y.')">';
    $svg .= '<text x="'.$x.'" y="'.$y.'" font-family="monospace" font-size="20" fill="'.$fg.'">'.htmlspecialchars($ch).'</text>';
    $svg .= '</g>';
}
$svg .= '</svg>';
echo $svg;
exit;
?>


