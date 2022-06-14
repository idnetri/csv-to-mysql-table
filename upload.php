<?php
  /**
  * @author Tri Purnomo
  * @author Tri Purnomo <emailetri@gmail.com>
  */

//https://www.w3schools.com/php/php_file_upload.asp
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}


// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000000) {
  echo "Sorry, your file is too large.";
  echo "<br>";
  echo "Your file size is ".$_FILES["fileToUpload"]["size"];
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "csv") {
  echo "Sorry, only CSV files are allowed.";
  echo "<br/>";
  echo "Your file is in ".$imageFileType." format.<br/>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Your file was not uploaded.<br>";
  echo  "<a href='index.php'>&larr;Back</a>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "uploads/".$_FILES["fileToUpload"]["name"])) {
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.<br>";
    echo  "<a href='index.php'>See All Uploaded Files</a>";
  } else {
    echo "Sorry, there was an error uploading your file.";
    echo  "<a href='index.php'>Back</a>";
  }
}
?>