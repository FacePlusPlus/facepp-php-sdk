<?php
require_once __DIR__ . '/../lib/Image.php';

use Fpp\Image;

$grayb64 = 'demo-segment.b64';
$data = file_get_contents($grayb64);
$stream = base64_decode($data);
$grayImage = imagecreatefromstring($stream);
/*
for ($i = 0; $i < 500; $i++) {
    for ($j = 0; $j < 500; $j++) {
        echo imagecolorat($grayImage, $i, $j);
    }
}
*/
$input = 'demo-segment.jpg';
$inputImage = imagecreatefromjpeg($input);

$background = 'demo-sence.jpg';
$bgImage = imagecreatefromjpeg($background);

$newImage = Image::humanbodyBlendingWithImageByGray($inputImage, $grayImage, $bgImage);
imagejpeg($newImage, 'demo-segment-background-result.jpg');


$bgColor = 0xFFFFFF;
$colorImage = Image::humanbodyBlendingWithColorByGray($inputImage, $grayImage, $bgColor);
imagejpeg($colorImage, 'demo-segment-color-result.jpg');

imagedestroy($grayImage);
imagedestroy($inputImage);
imagedestroy($bgImage);
imagedestroy($newImage);
imagedestroy($colorImage);

?>
