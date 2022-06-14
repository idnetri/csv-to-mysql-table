<?php

$get_filename = $_GET['filename'];

$fileName = 'uploads/'.$get_filename;

unlink($fileName);

header("Location: index.php");
exit();

?>