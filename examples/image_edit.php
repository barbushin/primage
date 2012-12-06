<?

require_once('../Primage/Primage.php');

$image = Primage::buildFromFile('original_images/clipart/bird.jpg');

$image->resize(400, 400);
$image->addEffect('blur');

$watermark = Primage::buildFromFile('original_images/watermark.png');
$image->addWatermark($watermark, -10, -10, 50);

$image->sendToStdout('jpg', 80);



