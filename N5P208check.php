<?php
//connect to MySQL
$db = mysqli_connect('localhost', 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

//change this path to match your images directory
$dir ='C:/Apache24/htdocs/ExtraGraphicsLibrary/images';

// handle the uploaded image
if ($_POST['submit'] == 'Upload') {

    //make sure the uploaded file transfer was successful
    if ($_FILES['uploadfile']['error'] != UPLOAD_ERR_OK)
    {
        switch ($_FILES['uploadfile']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            die('The uploaded file exceeds the upload_max_filesize directive ' .
                'in php.ini.');
            break;
        case UPLOAD_ERR_FORM_SIZE:
            die('The uploaded file exceeds the MAX_FILE_SIZE directive that ' .
                'was specified in the HTML form.');
            break;
        case UPLOAD_ERR_PARTIAL:
            die('The uploaded file was only partially uploaded.');
            break;
        case UPLOAD_ERR_NO_FILE:
            die('No file was uploaded.');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            die('The server is missing a temporary folder.');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            die('The server failed to write the uploaded file to disk.');
            break;
        case UPLOAD_ERR_EXTENSION:
            die('File upload stopped by extension.');
            break;
        }
    }
    
    //get info about the image being uploaded
    $image_caption = $_POST['caption'];
    $image_username = $_POST['username'];
    $image_date = @date('Y-m-d');
    list($width, $height, $type, $attr) =
        getimagesize($_FILES['uploadfile']['tmp_name']);

    // make sure the uploaded file is really a supported image
    $error = 'The file you uploaded was not a supported filetype.';
    switch ($type) {
    case IMAGETYPE_GIF:
        $image = imagecreatefromgif($_FILES['uploadfile']['tmp_name']) or
            die($error);
        break;
    case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($_FILES['uploadfile']['tmp_name']) or
            die($error);
        break;
    case IMAGETYPE_PNG:
        $image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']) or
            die($error);
        break;
    default:
        die($error);
    }

    //insert information into image table
    $query = 'INSERT INTO images
        (image_caption, image_username, image_date)
    VALUES
        ("' . $image_caption . '", "' . $image_username . '", "' . $image_date .
        '")';
    
    $result = mysqli_query($db, $query) or die (mysqli_error($db));
    
    //retrieve the image_id that MySQL generated automatically when we inserted
    //the new record
    $last_id = mysqli_insert_id($db);
    
    // save the image to its final destination
    $image_id = $last_id;
    imagejpeg($image, $dir . '/' . $image_id  . '.jpg');
    imagedestroy($image);

} else {
    // retrieve image information
    $query = 'SELECT
        image_id, image_caption, image_username, image_date
    FROM
        images
    WHERE
        image_id = ' . $_POST['id'];
    $result = mysqli_query($db, $query) or die (mysqli_error($db));
    extract(mysqli_fetch_assoc($result));

    list($width, $height, $type, $attr) = getimagesize($dir . '/' . $image_id .
        '.jpg');
}

if ($_POST['submit'] == 'Save') {
    // make sure the requested image is valid
    if (isset($_POST['id']) && ctype_digit($_POST['id']) &&
        file_exists($dir . '/' . $_POST['id'] . '.jpg')) {
        $image = imagecreatefromjpeg($dir . '/' . $_POST['id'] . '.jpg');
    } else {
        die('invalid image specified');
    }

    // apply the filter    
    $effect = (isset($_POST['effect'])) ? $_POST['effect'] : -1;
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

    // save the image with the filter applied
    imagejpeg($image, $dir . '/' . $_POST['id'] . '.jpg', 100);

?>
<html>
 <head>
  <title>Here is your pic!</title>
 </head>
 <body>
  <h1>Your image has been saved!</h1>
  <img src="images/<?php echo $_POST['id']; ?>.jpg" />
 </body>
</html>
<?php
} else {
?>
<html>
 <head>
  <title>Here is your pic!</title>
 </head>
 <body>
  <h1>So how does it feel to be famous?</h1>
  <p>Here is the picture you just uploaded to our servers:</p>
<?php 
   if ($_POST['submit'] == 'Upload') {
       $imagename = 'images/' . $image_id  . '.jpg';
   } else {
       $imagename = 'N5P209effect.php?id=' . $image_id  . '&e=' .
           $_POST['effect'];
   }
?>
   <img src="<?php echo $imagename; ?>" style="float:left;">
  <table>
   <tr><td>Image Saved as: </td><td><?php echo $image_id  . '.jpg'; ?></td></tr>
   <tr><td>Height: </td><td><?php echo $height; ?></td></tr>
   <tr><td>Width: </td><td><?php echo $width; ?></td></tr>
   <tr><td>Upload Date: </td><td><?php echo $image_date; ?></td></tr>
  </table>
  <p>You may apply special options to your image below. Note: saving an image
with any of the options applied  <em>cannot be undone</em>.</p>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
   <div>
    <input type="hidden" name="id" value="<?php echo $image_id;?>"/>
    Filter: <select name="effect">
     <option value="-1">None</option>
<?php
    echo '<option value="' . IMG_FILTER_GRAYSCALE . '"';
    if (isset($_POST['effect']) && $_POST['effect'] == IMG_FILTER_GRAYSCALE) {
        echo ' selected="selected"';
    }
    echo '>Black and White</option>';

    echo '<option value="' . IMG_FILTER_GAUSSIAN_BLUR . '"';
    if (isset($_POST['effect']) && $_POST['effect'] ==
        IMG_FILTER_GAUSSIAN_BLUR) {
        echo ' selected="selected"';
    }
    echo '>Blur</option>';

    echo '<option value="' . IMG_FILTER_EMBOSS . '"';
    if (isset($_POST['effect']) && $_POST['effect'] == IMG_FILTER_EMBOSS) {
        echo ' selected="selected"';
    }
    echo '>Emboss</option>';

    echo '<option value="' . IMG_FILTER_NEGATE . '"';
    if (isset($_POST['effect']) && $_POST['effect'] == IMG_FILTER_NEGATE) {
        echo ' selected="selected"';
    }
    echo '>Negative</option>';
?>
    </select>
    <input type="submit" value="Preview" name="submit" />
    <input type="submit" value="Save" name="submit" />
   </div>
  </form>
 </body>
</html>
<?php
}
?>
