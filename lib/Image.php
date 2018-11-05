<?php
namespace Fpp;

// calculate the red channel color value
function R($color) {
    return $color >> 16 & 0xFF;
}

// calculate the green channel color value
function G($color) {
    return $color >> 8 & 0xFF;
}

// calculate the blue channel color value
function B($color) {
    return $color & 0xFF;
}

// calculate the red channel color value
function Ra($color) {
    return $color >> 24 & 0xFF;
}

// calculate the green channel color value
function Ga($color) {
    return $color >> 16 & 0xFF;
}

// calculate the blue channel color value
function Ba($color) {
    return $color >> 8 & 0xFF;
}

// calculate the alpha channel color value
function Aa($color) {
    return $color & 0xFF;
}

/**
 * Face++ Image Process And Parse
 * such as replace the background of the human body image, or replace the background color
 */
class Image
{
    /**
     * blend the background and the segment body
     *
     * @param resource $inputImage the resource image, true color
     * @param resource $grayImage the segment response resource image of the face++ api return
     * @param resource $bgImage the resource image you want to blend with the human body
     * @return resource the resource image blended
     */
    public static function humanbodyBlendingWithImageByGray($inputImage, $grayImage, $bgImage)
    {
        // get the width and height of the image
        $inputWidth = imagesx($inputImage);
        $inputHeight = imagesy($inputImage);

        $bgWidth = imagesx($bgImage);
        $bgHeight = imagesy($bgImage);

        $inputAspectRatio = $inputWidth / $inputHeight;
        $bgAspectRatio = $bgWidth / $bgHeight;

        $targetWidth = 0;
        $targetHeight = 0;
        if ($bgAspectRatio > $inputAspectRatio) {
            $targetWidth = round($bgHeight * $inputAspectRatio);
            $targetHeight = $bgHeight;
        }else{
            $targetWidth = $bgWidth;
            $targetHeight = round($bgWidth / $inputAspectRatio);
        }

        $cropImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresized($cropImage, $bgImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $targetWidth, $targetHeight);

        $newImage = imagescale($cropImage, $inputWidth, $inputHeight);
        imagedestroy($cropImage);

        for ($x = 0; $x < $inputWidth; $x++) {
            for ($y = 0; $y < $inputHeight; $y++) {
                $grayColor = imagecolorat($grayImage, $x, $y);
                $confidence = (R($grayColor) + G($grayColor) + B($grayColor)) / 3.0 / 255.0;
                $inputColor = imagecolorat($inputImage, $x, $y);
                $bgColor = imagecolorat($newImage, $x, $y);

                $alpha = $confidence;
                $newR = R($inputColor) * $alpha + R($bgColor) * (1 - $alpha);
                $newG = G($inputColor) * $alpha + G($bgColor) * (1 - $alpha);
                $newB = B($inputColor) * $alpha + B($bgColor) * (1 - $alpha);

                $newR = max(0, min($newR, 255));
                $newG = max(0, min($newG, 255));
                $newB = max(0, min($newB, 255));

                $newColor = imagecolorallocate($newImage, $newR, $newG, $newB);
                // echo $newColor . "\t" . $inputColor . "\t" . $bgColor . "\t" . $alpha . "\n";
                imagesetpixel($newImage, $x, $y, $newColor);
            }
        }

        return $newImage;
    }

    /**
     * replace the background with the bgcolor
     * @param resource $inputImage
     * @param resource $grayImage
     * @param int $bgColor the RGB color value, for example 0xFFFFFF
     * @return resource image
     */
    public static function humanbodyBlendingWithColorByGray($inputImage, $grayImage, $bgColor)
    {
        // get the width and height of the image
        $inputWidth = imagesx($inputImage);
        $inputHeight = imagesy($inputImage);

        $newImage = imagecreatetruecolor($inputWidth, $inputHeight);

        for ($x = 0; $x < $inputWidth; $x++) {
            for ($y = 0; $y < $inputHeight; $y++) {
                $grayColor = imagecolorat($grayImage, $x, $y);
                $confidence = (R($grayColor) + G($grayColor) + B($grayColor)) / 3.0 / 255.0;
                $inputColor = imagecolorat($inputImage, $x, $y);

                $alpha = $confidence;
                $newR = R($inputColor) * $alpha + R($bgColor) * (1 - $alpha);
                $newG = G($inputColor) * $alpha + G($bgColor) * (1 - $alpha);
                $newB = B($inputColor) * $alpha + B($bgColor) * (1 - $alpha);

                $newR = max(0, min($newR, 255));
                $newG = max(0, min($newG, 255));
                $newB = max(0, min($newB, 255));

                $newColor = imagecolorallocate($newImage, $newR, $newG, $newB);
                // echo $newColor . "\t" . $inputColor . "\t" . $bgColor . "\t" . $alpha . "\n";
                imagesetpixel($newImage, $x, $y, $newColor);
            }
        }

        return $newImage;
    }

    public static function humanbodyBlendingWithImageByPng($inputImage, $pngImage, $bgImage)
    {
        // get the width and height of the image
        $inputWidth = imagesx($inputImage);
        $inputHeight = imagesy($inputImage);

        $bgWidth = imagesx($bgImage);
        $bgHeight = imagesy($bgImage);

        $scale = self::calculateTargetScale($inputWidth, $inputHeight, $bgWidth, $bgHeight);
        $targetWidth = $scale[0];
        $targetHeight = $scale[1];

        $cropImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresized($cropImage, $bgImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $targetWidth, $targetHeight);

        $newImage = imagescale($cropImage, $inputWidth, $inputHeight);
        imagedestroy($cropImage);

        for ($x = 0; $x < $inputWidth; $x++) {
            for ($y = 0; $y < $inputHeight; $y++) {
                $pngColor = imagecolorat($pngImage, $x, $y);
                echo $pngColor;
                echo "\n";
                echo Ra($pngColor);
                echo "\n";
                echo Ga($pngColor);
                echo "\n";
                echo Ba($pngColor);
                echo "\n";
                echo Aa($pngColor);
                echo "\n";
                /*
                $confidence = (R($grayColor) + G($grayColor) + B($grayColor)) / 3.0 / 255.0;
                $inputColor = imagecolorat($inputImage, $x, $y);
                $bgColor = imagecolorat($newImage, $x, $y);

                $alpha = $confidence;
                $newR = R($inputColor) * $alpha + R($bgColor) * (1 - $alpha);
                $newG = G($inputColor) * $alpha + G($bgColor) * (1 - $alpha);
                $newB = B($inputColor) * $alpha + B($bgColor) * (1 - $alpha);

                $newR = max(0, min($newR, 255));
                $newG = max(0, min($newG, 255));
                $newB = max(0, min($newB, 255));

                $newColor = imagecolorallocate($newImage, $newR, $newG, $newB);
                // echo $newColor . "\t" . $inputColor . "\t" . $bgColor . "\t" . $alpha . "\n";
                imagesetpixel($newImage, $x, $y, $newColor);
                */
            }
        }

        return $newImage;
    }


    public static function calculateTargetScale($inputWidth, $inputHeight, $bgWidth, $bgHeight)
    {
        $inputAspectRatio = $inputWidth / $inputHeight;
        $bgAspectRatio = $bgWidth / $bgHeight;

        $targetWidth = 0;
        $targetHeight = 0;
        if ($bgAspectRatio > $inputAspectRatio) {
            $targetWidth = round($bgHeight * $inputAspectRatio);
            $targetHeight = $bgHeight;
        }else{
            $targetWidth = $bgWidth;
            $targetHeight = round($bgWidth / $inputAspectRatio);
        }

        return array($targetWidth, $targetHeight);
    }

}
