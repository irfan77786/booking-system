<?php
$base = dirname(__DIR__);
$src = $base . '/public/assets/img/site/black-car-service-dallas-logo.webp';
$dst = $base . '/public/assets/img/site/black-car-service-dallas-logo.png';
if (!is_readable($src)) {
    fwrite(STDERR, "Missing: $src\n");
    exit(1);
}
$img = imagecreatefromwebp($src);
if (!$img) {
    fwrite(STDERR, "Could not read webp\n");
    exit(1);
}
imagealphablending($img, false);
imagesavealpha($img, true);
imagepng($img, $dst);
imagedestroy($img);
echo "ok " . filesize($dst) . "\n";
