<?php
//connect to MySQL
$db = mysqli_connect('localhost', 'root', 'root') or 
    die ('Unable to connect. Check your connection parameters.');
mysqli_select_db( $db,'moviesite') or die(mysqli_error($db));

//change this path to match your images directory
$dir ='images';

//change this path to match your thumbnail directory
$thumbdir = $dir . '/thumbs';
?>
<html>
 <head>
  <title>Welcome to our Photo Gallery</title>
  <style type="text/css">
   th { background-color: #999;}
   .odd_row { background-color: #EEE; }
   .even_row { background-color: #FFF; }
  </style>
 </head>
 <body>
  <p>Click on any image to see it full sized.</p>
  <table style="width:100%;">
   <tr>
    <th>Image</th>
    <th>Caption</th>
    <th>Uploaded By</th>
    <th>Date Uploaded</th>
   </tr>
<?php
//get the thumbs
$result = mysqli_query($db,'SELECT * FROM images') or die(mysqli_error());

$odd = true;
while ($rows = mysqli_fetch_array($result)) {
    echo ($odd == true) ? '<tr class="odd_row">' : '<tr class="even_row">';
    $odd = !$odd; 
    extract($rows);
    echo '<td><a href="' . $dir . '/' . $image_id . '.jpg">';
    echo '<img src="' . $thumbdir . '/' . $image_id . '.jpg">';
    echo '</a></td>';
    echo '<td>' . $image_caption . '</td>';
    echo '<td>' . $image_username . '</td>';
    echo '<td>' . $image_date . '</td>';
    echo '</tr>';
}
?>
  </table>
 </body>
</html>
