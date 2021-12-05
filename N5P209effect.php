<?php
//change this path to match your images directory
$dir ='C:/Apache24/htdocs/ExtraGraphicsLibrary/images';

//change this path to match your fonts directory and the desired font
putenv('GDFONTPATH=' . 'C:/Windows/Fonts');
$font = 'arial';

// make sure the requested image is valid
if (isset($_GET['id']) && ctype_digit($_GET['id']) && file_exists($dir . '/' .
    $_GET['id'] . '.jpg')) {
    $image = imagecreatefromjpeg($dir . '/' . $_GET['id'] . '.jpg');
} else {
    die('invalid image specified');
}

// apply the filter
$effect = (isset($_GET['e'])) ? $_GET['e'] : -1;
switch ($effect) {
case IMG_FILTER_NEGATE:
    imagefilter($image, IMG_FILTER_NEGATE); 
    break;
case IMG_FILTER_GRAYSCALE:
    imagefilter($image, IMG_FILTER_GRAYSCALE); 
    break;
case IMG_FILTER_EMBOSS:
    imagefilter($image, IMG_FILTER_EMBOSS); 
    break;
case IMG_FILTER_GAUSSIAN_BLUR:
    imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
    break;
}

// add the caption if requested
if (isset($_GET['capt'])) {
    imagettftext($image, 12, 0, 20, 20, 0, $font, $_GET['capt']);
}

//add the logo watermark if requested
if (isset($_GET['logo'])) {
    // determine x and y position to center watermark
    list($width, $height) = getimagesize($dir . '/' . $_GET['id'] . '.jpg');
    list($wmk_width, $wmk_height) = getimagesize('images/logo.png');
    $x = ($width - $wmk_width) / 2;
    $y = ($height - $wmk_height) / 2;
    
    $wmk = imagecreatefrompng('images/logo.png');
    imagecopymerge($image, $wmk, $x, $y, 0, 0, $wmk_width, $wmk_height, 20);
    imagedestroy($wmk);
}

// show the image
header('Content-Type: image/jpeg');
imagejpeg($image, '', 100);
?>
