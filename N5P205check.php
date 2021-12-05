<?php
//connect to MySQL
$db = mysqli_connect('localhost', 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

//change this path to match your images directory
$dir ='C:/Apache24/htdocs/ExtraGraphicsLibrary/images';

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
           $ext = '.gif';
        break;
    case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($_FILES['uploadfile']['tmp_name']) or
           die($error);
           $ext = '.jpg';
        break;
    case IMAGETYPE_PNG:
        $image = imagecreatefrompng($_FILES['uploadfile']['tmp_name']) or
           die($error);
           $ext = '.png';
        break;
    default:
        die($error);
}

//insert information into image table
$query = 'INSERT INTO images(image_caption, image_username, image_date)
    VALUES
        ("' . $image_caption . '", "' . $image_username . '", "' . $image_date .
        '")';
    
$result = mysqli_query($db, $query) or die (mysqli_error($db));
    
//retrieve the image_id that MySQL generated automatically when we inserted
//the new record
$last_id = mysqli_insert_id($db);
    
// because the id is unique, we can use it as the image name as well to make
//sure we don't overwrite another image that already exists
$imagename = $last_id . $ext;

//update the image table now that the final filename is known
$query = 'UPDATE images
		SET image_filename = "' . $imagename . '"
		WHERE image_id = ' . $last_id;
$result = mysqli_query($db, $query) or die (mysqli_error($db));

//save the image to its final destination
switch ($type) {
	case IMAGETYPE_GIF:
		imagegif($image, $dir . '/' . $imagename);
		break;
	case IMAGETYPE_JPEG:
		imagejpeg($image, $dir . '/' . $imagename, 100);
		break;
	case IMAGETYPE_PNG:
		imagepng($image, $dir . '/' . $imagename);
		break;
}

imagedestroy($image);
?>
<html>
 <head>
  <title>Here is your pic!</title>
 </head>
 <body>
  <h1>So how does it feel to be famous?</h1>
  <p>Here is the picture you just uploaded to our servers:</p>
  <img src="images/<?php echo $imagename; ?>" style="float:left;">
  <table>
	  <tr><td>Image Saved as:</td><td><?php echo $imagename; ?></td></tr>
	  <tr><td>Image Type:</td><td><?php echo $ext; ?></td></tr>
	  <tr><td>Height:</td><td><?php echo $height; ?></td></tr>
	  <tr><td>Width:</td><td><?php echo $width; ?></td></tr>
	  <tr><td>Upload Date:</td><td><?php echo $image_date; ?></td></tr>	  
  </table>
 </body>
</html>
