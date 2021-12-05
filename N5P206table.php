<?php
$db = mysqli_connect('localhost', 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db($db, 'moviesite') or die(mysqli_error($db));

//create the images table
$query = 'ALTER TABLE images DROP COLUMN image_filename';

mysqli_query($db, $query) or die (mysqli_error($db));

echo 'Images table successfully updated.';
?>
