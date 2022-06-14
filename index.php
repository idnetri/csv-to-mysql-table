<?php
  /**
  * @author Tri Purnomo
  * @author Tri Purnomo <emailetri@gmail.com>
  */
  ?>

<!DOCTYPE html>
<html>
<body>
<h2>Welcome to Simple CSV To Database Applications</h2>
<form action="upload.php" method="post" enctype="multipart/form-data">
  Select CSV File to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload CSV File" name="submit">
</form>
<br/>
<br/>
<b>Uploaded Files to import</b>
<br/>
<?php   
  $mydir = 'uploads'; 
  
  $myfiles = array_diff(scandir($mydir), array('.', '..')); 
  
  $no = 1;
  foreach ($myfiles as $row) {
	  echo "<a href='import.php?filename=".$row."'>".$no.". ".$row."</a> - <a href='deletefile.php?filename=".$row."'> x </a><br/>";
	$no++;	
  }

?> 


</body>
</html>
