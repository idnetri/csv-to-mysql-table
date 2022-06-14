<?php
  /**
  * @author Tri Purnomo
  * @author Tri Purnomo <emailetri@gmail.com>
  */
  ?>

<a href="index.php">Home</a><br><br>

<?php 

  include 'config.php';
  
  $mydir = 'uploads'; 
  
  $myfiles = array_diff(scandir($mydir), array('.', '..')); 
  

  $get_filename = $_GET['filename'];

  $fileName = 'uploads/'.$get_filename;

  if(!isset($_POST['submit']))
  {

    $get_filename = $_GET['filename'];

    $fileName = 'uploads/'.$get_filename;

    $csv = array_map("str_getcsv", file('uploads/'.$get_filename,FILE_SKIP_EMPTY_LINES));
    $keys = array_shift($csv);

    if(strpos($keys[0], ";") !== false){ //If semicolon separated
      $newKeys = explode(";", $keys[0]);
      $separator = ";";
      echo "Semicolon separated";
    } else{
        echo "Comma Separated";
        $newKeys = $keys;
        $separator = ",";
    }


    echo '<form action="'.$_SERVER['PHP_SELF'].'?&filename='.$get_filename.'" method="POST">
    Filename:
    <br>
    '.$fileName.'
    <br><br>';

    echo 'Headers in csv :<br>';
    foreach ($newKeys as $key =>$row) {
      if ($row != "") {
        echo $row.',';
      }  
    }

    echo '<br><br>';

    echo '<h4>DB Config</h4>';
    $data = mysqli_query($conn, "SELECT * FROM tbldblist limit 1");

    while($d = mysqli_fetch_array($data)){
      //print_r($d);
      echo '
      <input type="hidden" name="id" value="'. $d['id'].'">
      DB HOST : <input type ="text" name="dbhost" required="" value="'. $d['dbhost'].'">
      DB User : <input type ="text" name="dbuser" required="" value="'. $d['dbuser'].'">
      DB Pass : <input type ="text" name="dbpass" required="" value="'. $d['dbpass'].'">
      Port : <input type ="text" name="dbport" required="" value="'. $d['dbport'].'"><br>
      DB Name : <input type ="text" name="dbname" required="" value="'. $d['dbname'].'">
      <br>
      <br>
      ';
    }


    echo 'Input table name:
    <br>
    <input type ="text" name="tablename" value="" required="">
    <br><br>
    <input type="submit" name="submit" value="Process Import">

  </form>';
  }

  else 
  
  {

    $tableName = "zz".$_POST['tablename'];

    //set connection 2
    $conn2 = new mysqli($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass'],$_POST['dbname'],$_POST['dbport']);
    if($conn2->connect_error)
    {
      die('Failed Connect ! '.$conn->connect_error);
    }

    $sqlupdateconfigdb = "update tbldblist 
    set dbhost = '".$_POST['dbhost'].
    "', dbuser = '".$_POST['dbuser'].
    "', dbpass = '".$_POST['dbpass'].
    "', dbname = '".$_POST['dbname'].
    "', dbport = '".$_POST['dbport'].
    "' where id = ".$_POST['id'];

   //echo $sqlupdateconfigdb; die();
    $updateconfig = mysqli_query($conn,$sqlupdateconfigdb);

    if ($updateconfig) {
      //echo 'Table '.$tableName.' created';
    } else {
      echo 'Update table Failed!<br>';
      echo mysqli_error($conn);
      die();
    }

    $get_filename = $_GET['filename'];

    $fileName = 'uploads/'.$get_filename;

    $csv = array_map("str_getcsv", file('uploads/'.$get_filename,FILE_SKIP_EMPTY_LINES));
    $keys = array_shift($csv);

    if(strpos($keys[0], ";") !== false){ //If semicolon separated
      $newKeys = explode(";", $keys[0]);
      $separator = ";";
    } else{
        $newKeys = $keys;
        $separator = ",";
    }

    $filteredCount = count(array_filter($newKeys));

    $createTableQuery = 'CREATE TABLE '.$tableName.' (';
    $columnsName = '';


    foreach ($newKeys as $key =>$row) {
      $uselesscharacter = array("#", " ", "(", ")", "+", "-", "*", "!", "=", "?","/","'",".");
      $columnStructure = str_replace($uselesscharacter,"",$row);

      if ($columnStructure == "Database") {
        $columnStructure = $columnStructure.'_name';
      }
      
      if ($row != "") {
        if ($key < $filteredCount-1) {
          $columnsName = $columnsName.$columnStructure.',';
          $createTableQuery = $createTableQuery.$columnStructure.' VARCHAR(500),';
        } else {
          $columnsName = $columnsName.$columnStructure;
          $createTableQuery = $createTableQuery.$columnStructure.' VARCHAR(500) )';
        }
      }
    }


    $createdTable=mysqli_query($conn2,$createTableQuery);

    if ($createdTable) {
    } else {
      echo 'Create table Failed!<br>';
      echo mysqli_error($conn2);
      echo '<br/>';
      echo "<a href=\"javascript:history.go(-1)\">Back</a>";
      die();
    }


    $file = $fileName;
	
    $base_path = realpath($file);

    $file = str_replace('\\', '/', $base_path);
    
    $importQuery = <<<eof
    LOAD DATA LOW_PRIORITY 
    LOCAL INFILE '$file'
    IGNORE INTO TABLE `$tableName` CHARACTER SET latin1 
    FIELDS TERMINATED BY '$separator' OPTIONALLY ENCLOSED BY '"' 
    ESCAPED BY '"' LINES TERMINATED BY '\r\n' IGNORE 1 LINES 
    ($columnsName);
    eof;
      
      // https://www.pakainfo.com/check-query-execution-time-php/
      $started = microtime(true);

      $importData=mysqli_query($conn2,$importQuery);

      if ($importData) {
        echo '<h4>Data Imported to table '.$tableName.' successfully!</h4>';
      } else {
	//echo $importQuery;
        echo 'Data Import Failed!<br>';
        echo mysqli_error($conn);
      }

      
      $end = microtime(true);

      $total_diff = $end - $started;
      

      $getqueryTime = number_format($total_diff, 10);
      
      echo "SQL query took $getqueryTime seconds.<br/>";

      echo "Affected rows: " . mysqli_affected_rows($conn2);

      echo "<br>".$_POST['dbhost']."/".$_POST['dbname'];
  }
  
?> 


